# Changelog — Bíblia Show 2.0

## [v0.1.1] — 2026-11-09

### Multijogador em tempo real (P1)

- Modelos: `Participante`, `Equipe`, `Resposta` com SoftDeletes
- Migrações: `participantes`, `equipes`, `respostas`
- Relacionamentos: `Sala` → participantes/equipes/jogoAtivo; `Jogo` → respostas;
  `Participante` → sala/equipe/respostas; `Equipe` → sala/participantes/respostas;
  `Resposta` → jogo/questao/participante/equipe
- Eventos WebSocket (ShouldBroadcast → Reverb): `ParticipanteEntrou`,
  `PerguntaIniciada`, `RespostaEnviada`, `RankingAtualizado`, `JogoEstadoAlterado`
- Canais de broadcast privados: `game.{salaId}` com autenticação por igreja
- `ParticipantController`: entrar na sala via código, responder pergunta,
  listar participantes/equipes, sair
- `ApresentadorController`: criar equipe, broadcast de estado/pergunta/ranking
- Rotas API: `api/salas/entrar`, `api/salas/{sala}`, `api/salas/{sala}/responder`,
  `api/salas/{sala}/participantes/{participante}`

## [v0.1.0] — 2026-11-09

### Estrutura inicial do projeto

- Scaffold Laravel 13 + PHP 8.4
- Laravel Reverb instalado e configurado (WebSockets)
- Laravel Boost instalado (guidelines, skills, MCP)
- `.env`: `APP_NAME=Bíblia Show`, `APP_LOCALE=pt`, `BROADCAST_CONNECTION=reverb`
- `ARCHITETURA.md` na raiz com especificações A–H
- `docs/` com ARCHITETURA, MODELO_DE_DADOS, API, INSTALACAO, TESTES
- Matriz de prioridades P0/P1/P2 e critérios de aceitação
- Testes a passar (2 testes, 2 asserções)

### Modelos base e motor de jogo

- Modelos: `Igreja`, `Usuario`, `LivroBiblico`, `Capitulo`, `Categoria`,
  `NivelDificuldade`, `Questao`, `Sala`, `Jogo`
- Migrações: `igrejas`, `usuarios`, `livros_biblicos`, `capitulos`,
  `categorias`, `niveis_dificuldade`, `questoes`, `salas`, `jogos`
- Motor de jogo modular: contratos, seletor aleatório, pontuação,
  temporizador, estado, eventos, `GameEngine`
- Testes unitários do motor: instanciação, início, resposta correta,
  resposta incorreta, não repetição de perguntas (7 testes, 10 asserções)

### Modo individual offline (P0)

- `GameController`: jogar, responder, resultado com sessão
- Rotas: `jogar`, `jogar/responder`, `jogar/resultado`
- Views: `game/play`, `game/result` com Tailwind CSS
- Motor de jogo persistido na sessão para continuar após recarregar
- Temporizador regressivo com envio automático ao esgotar
- Feedback de acerto/erro com explicação e pontos

### Modo apresentador e projeção (P0)

- `ApresentadorController`: painel, criar sala, controle (próxima/pausar/retomar/finalizar), resultado
- `ProjecaoController`: tela de projeção pública independente
- Rotas: `apresentador.*`, `projecao.tela`
- Views: `apresentador/painel`, `apresentador/control`, `apresentador/resultado`
- Views: `projecao/tela`, `projecao/espera` (sem controles administrativos)
- Design moderno com Tailwind CSS para projetores e TVs

### Sistema de gestão de perguntas (P0)

- `PerguntaController`: criar, listar, editar, visualizar, aprovar, rejeitar perguntas
- Rotas web: `perguntas.*` (index, create, store, show, edit, update, destroy, aprovar, rejeitar)
- Rotas API: `api/livros/{id}/capitulos` para carregamento dinâmico de capítulos
- `Api\LivroController`: endpoint de capítulos por livro
- Views: `perguntas/index`, `create`, `edit`, `show` com Tailwind CSS
- Middleware `IgrejaScope`: isolamento por `igreja_id` em todas as requisições
- Seeders: `LivrosBiblicosSeeder` (66 livros), `NiveisDificuldadeSeeder` (5 níveis),
  `CategoriasSeeder` (8 categorias), `QuestoesExemploSeeder` (2 perguntas exemplo)
- Modelos: `$table` definido para `LivroBiblico`, `NivelDificuldade`, `Categoria`
- Validação de referências bíblicas (livro/capítulo/versículo existem)

