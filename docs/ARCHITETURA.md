# Arquitetura — Bíblia Show 2.0

Resumo executivo da arquitetura do produto. Detalhe completo em `ARCHITETURA.md` na raiz.

## Stack

| Camada | Tecnologia |
|--------|-----------|
| Backend | Laravel 13 + PHP 8.4 |
| Frontend | Blade + Alpine.js + Tailwind CSS |
| Banco de dados | SQLite (dev/offline) / MySQL ou PostgreSQL (produção) |
| Tempo real | Laravel Reverb (WebSockets) |
| Cache / Sessões | File / Redis (opcional) |
| Mobile futuro | Capacitor (Android) |
| Documentação API | OpenAPI |

## Módulos principais

- **Motor de jogo** (`App\Game\Engine`): modular, independente da UI, gere seleção de perguntas, pontuação, temporizadores, estados e resultados.
- **Multijogador**: salas, código de acesso, QR Code, equipas, ranking dinâmico via WebSocket.
- **Apresentador / Projeção**: duas interfaces independentes (controlo administrativo vs ecrã público).
- **Biblioteca de conteúdos**: perguntas por livro/capítulo/versículo, categorias, dificuldade, públicos-alvo.
- **Gestão de perguntas**: criação manual, import/export Excel/CSV, fluxo editorial, histórico, deteção de duplicados.
- **Perfis**: 6 perfis com isolamento multi-tenant lógico por igreja.

## Prioridades

- **P0**: MVP indispensável (motor, offline, criação de perguntas, apresentador/projeção, aprendizagem).
- **P1**: primeira evolução (multijogador, import/export, audit log, submissão de colaboradores).
- **P2**: futuro (Android Capacitor, desktop, multi-tenant físico, inglês).
