# Arquitetura — Bíblia Show 2.0

Resumo executivo da arquitetura do produto. Detalhe completo em `ARCHITETURA.md` na raiz.

## Estado atual

MVP em desenvolvimento — P0 completo, P1 parcialmente implementado:
- ✅ Motor de jogo modular (`App\Game\Engine`)
- ✅ Modo individual offline sem conta
- ✅ Modo apresentador (controle de navegação, pausa/retomar, finalizar)
- ✅ Ecrã de projeção independente (sem controles administrativos)
- ✅ Resposta correta + explicação após cada pergunta
- ✅ Multijogador em tempo real (P1) — salas, código de acesso, WebSocket/Reverb
- ⬜ Criação manual de perguntas (P1)
- ⬜ Importação Excel/CSV (P1)

## Stack

| Camada | Tecnologia | Status |
|--------|-----------|--------|
| Backend | Laravel 13 + PHP 8.4 | Implementado |
| Frontend | Blade + Tailwind CSS | Implementado |
| Banco de dados | SQLite | Implementado |
| Tempo real | Laravel Reverb (WebSockets) | Configurado — eventos e canais privados `game.{salaId}` |
| Cache / Sessões | File / Redis (opcional) | Implementado |
| Mobile futuro | Capacitor (Android) | P2 |
| Documentação API | OpenAPI | P1 (docs/API.md) |
| Autenticação | Sanctum | P1 |

## Módulos principais

- **Motor de jogo** (`App\Game\Engine`): modular, independente da UI, gere seleção de perguntas, pontuação, temporizadores, estados e resultados. Implementado com 7 testes unitários.
- **Multijogador**: implementado (salas, código de acesso, WebSocket/Reverb, eventos, ranking em tempo real).
- **Apresentador / Projeção**: duas interfaces independentes implementadas (controle administrativo vs ecrã público).
- **Biblioteca de conteúdos**: modelos e migrações criados (livros, capítulos, categorias, dificuldade, questões). Sem UI de gestão ainda.
- **Gestão de perguntas**: planeado para P1 (criação manual, import/export, fluxo editorial).
- **Perfis**: 6 perfis definidos no modelo `Usuario` com isolamento lógico por `igreja_id`.

## Prioridades

- **P0**: MVP indispensável (motor, offline, apresentador/projeção, aprendizagem) — parcialmente implementado.
- **P1**: primeira evolução (multijogador, import/export, audit log, submissão de colaboradores).
- **P2**: futuro (Android Capacitor, desktop, multi-tenant físico, inglês).

## Testes

7 testes unitários passando, 10 asserções (`tests/Unit/GameEngineTest.php`).
