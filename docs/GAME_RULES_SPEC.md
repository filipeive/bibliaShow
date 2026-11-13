# Especificação Comum de Regras do Jogo — Bíblia Show 2.0

Este documento define as regras canónicas do modo Show/Individual partilhadas entre o **Backend Laravel** (`app/Game/Engine/GameEngine.php` + `StandardScoring.php`) e a **Aplicação Flutter** (`mobile/lib/services/game_engine_service.dart`).

---

## 1. Estrutura da Partida e Quatro Fases

Uma partida completa é composta por **24 perguntas** distribuídas em **4 fases** progressivas (6 perguntas por fase):

| Fase | Perguntas (1-based) | Índice (0-based) | Valor no Placar do Milhão |
| :--- | :---: | :---: | :---: |
| **Fase 1** | 1 a 6 | 0 a 5 | `10` pontos |
| **Fase 2** | 7 a 12 | 6 a 11 | `20` pontos |
| **Fase 3** | 13 a 18 | 12 a 17 | `30` pontos |
| **Fase 4 (Milhão)** | 19 a 24 | 18+ | `50` pontos |

---

## 2. Pontuação: Acertar, Parar e Errar

### 2.1. Placar do Milhão (`getPlacarMilhao()` / Getters Flutter)
- **Acertar:** `pontuacao_atual + valor_pergunta_fase` (`10`, `20`, `30` ou `50`).
- **Parar:** Mantém `100%` da `pontuacao_atual` acumulada e encerra a partida com segurança.
- **Errar:**
  - **Fases 1, 2 e 3 (`fase < 4`):** Mantém `floor(pontuacao_atual / 2)` (metade da pontuação acumulada).
  - **Fase 4 (`fase >= 4`):** `0` pontos (regra "Tudo ou Nada" da Fase Final).

### 2.2. Pontuação de Ranking / Velocidade no Backend Laravel (`StandardScoring`)
No motor Laravel (`GameEngine::answerQuestion()`), para efeitos de desempate e ranking web:
- Resposta incorreta: `0` pontos.
- Resposta correta: `100` (base) + `50` (bónus de rapidez se `tempoResposta <= 30s`) + `min(sequenciaAcertos * 10, 100)`.
- No Flutter offline (`GameEngineService`), o score acumulado segue diretamente `valorPerguntaAtual` (`10 / 20 / 30 / 50`) exibido no Placar do Milhão, aplicando `valorAoErrar` quando a partida é encerrada por limite de erros ou tempos esgotados.

---

## 3. Ajudas Disponíveis por Partida

| Ajuda | Quantidade / Partida | Limite por Pergunta | Regra de Funcionamento |
| :--- | :---: | :---: | :--- |
| **Cartas (50/50)** | `1` | 1 vez na partida | Elimina aleatoriamente **2 alternativas incorretas**, mantendo a correta visível. |
| **Dicas Bíblicas** | `3` | Máx. `1` por pergunta | Exibe `referencia_biblica`; na ausência desta, `explicacao`/`contexto`; ou `livro_nome`. |
| **Convidados Bíblicos** | `3` | Máx. `1` por pergunta | Consulta 1 dos 3 personagens bíblicos (`Paulo`, `Pedro`, `Moisés`). No backend Laravel, um dos 3 pode não saber a época do relato (`errado`); os demais indicam a alternativa correta. |
| **Pulos** | `3` | Sem limite por pergunta (enquanto > 0) | Avança/troca a pergunta atual sem contar erro, reiniciando o cronómetro de 60s e limpando as ajudas visuais da rodada anterior. |

---

## 4. Temporizador

- Cada pergunta possui um limite fixo de **`60` segundos**.
- Nos últimos `10` segundos (`tempoRestante <= 10`), são emitidos avisos sonoros de tick.
- Ao atingir `0` segundos (`tempo_esgotado`), a pergunta é encerrada sem pontuação, incrementa o contador `tempos_esgotados` e avança para a próxima pergunta (ou encerra se atingir o limite).

---

## 5. Condições de Finalização da Partida

A partida termina (`estado = terminado` / `GameStatus.finished`) quando ocorre qualquer uma das seguintes condições:

1. **Conclusão das 24 perguntas** (vitória completa).
2. **Parada voluntária (`Parar`):** o jogador garante `100%` da pontuação atual.
3. **Limite de erros (`maxErros = 3`):** ao atingir `3` respostas erradas na partida (aplica penalidade `valorAoErrar` no placar offline; `0` na Fase 4).
4. **Limite de tempos esgotados (`maxTemposEsgotados = 3`):** ao esgotar o cronómetro `3` vezes na partida.

---

## 6. Notas de Equivalência

- Ambas as plataformas utilizam comparação case-insensitive com `trim` para validar `resposta` vs `resposta_correta`.
- Em ambas as plataformas, mudar para uma nova pergunta limpa `opcoesEliminadas`, `dicaAtiva`/`dicaBiblicaAtual` e `convidadoAtivo`/`dicaConvidadoAtual`.
- Mudanças de fase (1 → 2 → 3 → 4) disparam a vinheta de abertura da nova fase.
