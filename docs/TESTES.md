# Testes e Critérios de Aceitação — Bíblia Show 2.0

## Estratégia de Testes

- **PHPUnit (`tests/Unit/GameEngineTest.php`)**: Valida todas as regras de negócio do `GameEngine` isoladamente da camada HTTP.
- **Testes de Fluxo HTTP e API**: Validam rotas públicas, autenticação (`/login`, `/registro`), painel administrativo (`/admin`), estúdio do apresentador (`/apresentador`), telão (`/projecao/{sala}`) e API de smartphones (`/api/salas/*`).

## Critérios de Aceitação por Módulo

### 1. Motor de Jogo Clássico (`GameEngine`)

| Critério | Teste Verificável |
|----------|-------------------|
| Partida completa e transição de estados | `test_inicia_partida_e_retorna_estado_em_curso` |
| Resposta correta e cálculo de pontos | `test_resposta_correta_retorna_acertou_e_pontos` |
| Resposta incorreta zera pontos da rodada | `test_resposta_incorreta_retorna_sem_pontos` |
| Seleção sem repetição de perguntas | `test_selecao_nao_repete_perguntas` |
| Ajuda das Cartas (50/50) elimina 2 incorretas | `test_ajuda_cartas_elimina_duas_opcoes_incorretas` |
| Ajuda dos Convidados Bíblicos (2 acertam, 1 não sabe) | `test_ajuda_convidados_biblicos_respeita_convidado_errado` |
| Pular pergunta substitui questão e decrementa pulos | `test_usar_pulo_substitui_pergunta_e_decrementa_pulos` |
| Dica Bíblica retorna referência e decrementa dicas | `test_usar_dica_biblica_retorna_referencia_e_decrementa_dicas` |

### 2. Salas Ao Vivo, Apresentador, Telão e Smartphones

| Critério | Teste Verificável |
|----------|-------------------|
| Criação de sala com 4 fases progressivas | `POST /apresentador` gera sala com código de 6 caracteres |
| Acionamento de ajudas pelo apresentador | `POST /apresentador/control/{sala}/ajuda` (`dica`, `convidado`, `cartas`, `pulo`, `revelar`) |
| Sincronização no Telão e Smartphones | `GET /api/salas/{sala}` retorna `fase_atual`, `placar_milhao` e `ajudas` em tempo real |
| Pontuação por fase nas salas | Resposta correta soma `10`, `20`, `30` ou `50` pts conforme a fase |

### 3. Autenticação e Painel Administrativo (`/admin`)

| Critério | Teste Verificável |
|----------|-------------------|
| Login e Cadastro | `GET/POST /login`, `GET/POST /registro`, `POST /logout` |
| Painel de Controle | `GET /admin` exibe KPIs, salas, igrejas, usuários e distribuição por fases |
| Exportação do Acervo | `GET /admin/exportar-perguntas?formato=csv` e `formato=json` |
| Reimportação `cadastro.dbf` | `POST /admin/reimportar-classico` sincroniza as 660 questões |

## Como Executar

```bash
# Todos os testes automatizados
php artisan test

# Apenas testes unitários do motor de jogo
php artisan test --testsuite=Unit
```
