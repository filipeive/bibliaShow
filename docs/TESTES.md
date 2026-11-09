# Testes e Critérios de Aceitação — Bíblia Show 2.0

## Estratégia de testes

- PHPUnit para testes unitários e de funcionalidade.
- Testes de browser (Dusk/Cypress) para fluxos de apresentador/projeção e multijogador.
- Testes de isolamento multi-tenant (igreja A não vê dados da igreja B).

## Critérios de aceitação por módulo

### Motor de jogo (G)

| Critério | Teste verificável |
|----------|-------------------|
| Partida completa sem interface | Teste unitário `GameEngineTest` — verde |
| Seleção aleatória sem repetição | Verificar que não há questões repetidas na partida |
| Pontuação correta | Simular respostas e verificar pontuação esperada |
| Estado da partida | Aguardando → em curso → pausado → terminado |

### Modo individual offline (G, E)

| Critério | Teste verificável |
|----------|-------------------|
| Jogar sem conta | Acessar `/jogar` sem autenticação |
| Sem internet | Funciona com SQLite local |
| Persistência de sessão | Fechar e reabrir navegador, continuar partida |

### Criação de perguntas (D)

| Critério | Teste verificável |
|----------|-------------------|
| Pergunta válida salva | POST `/perguntas` → código 302/201, registro no banco |
| Referência inválida rejeitada | Capítulo inexistente → erro 422 de validação |

### Fluxo editorial (D)

| Critério | Teste verificável |
|----------|-------------------|
| Pergunta pendente não jogável | Colaborador submete → não selecionável pelo motor |
| Após aprovação fica jogável | Editor aprova → selecionável |

### Multijogador em tempo real (A, P1)

| Critério | Teste verificável |
|----------|-------------------|
| Isolamento por sala | Dois clientes em salas distintas não veem dados uns dos outros |
| Ranking atualizado | Resposta envia evento WebSocket e ranking é atualizado |

### Apresentador / Projeção (B)

| Critério | Teste verificável |
|----------|-------------------|
| Projeção sem controles admin | `/projecao/{sala}` não mostra seletores de controlo |
| Apresentador com controles | `/apresentador/{sala}` mostra navegação, temporizador, pontuações |

### Aprendizagem (F)

| Critério | Teste verificável |
|----------|-------------------|
| Resposta correta após responder | Presença do campo resposta correta na resposta |
| Referência bíblica | Presença do campo referência |
| Explicação contextual | Presença do campo explicação |

### Isolamento multi-tenant (E)

| Critério | Teste verificável |
|----------|-------------------|
| Igreja A não vê Igreja B | Autenticar como admin Igreja A → listar questões → nenhuma de Igreja B |

### Desempenho (8.2)

| Critério | Teste verificável |
|----------|-------------------|
| Primeira carga < 3s (3G simulado) | Lighthouse ou medição de tempo de carregamento |

## Como executar

```bash
# Todos os testes
php artisan test

# Testes unitários apenas
php artisan test --testsuite=Unit

# Testes de funcionalidade
php artisan test --testsuite=Feature
```
