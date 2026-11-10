# API & Rotas — Bíblia Show 2.1

Documentação dos endpoints REST (`/api/*`) e rotas web principais do **Bíblia Show**.

## Endpoints REST (`/api/*`)

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/api/livros/{livro}/capitulos` | Lista os capítulos de um livro bíblico |
| `POST` | `/api/salas/entrar` | Regista um participante numa sala ao vivo pelo `codigo_acesso` (6 caracteres) |
| `GET` | `/api/salas/{sala}` | Retorna o estado completo da sala (`estado`, `fase_atual`, `placar_milhao`, `ajudas`, `pergunta_atual`, `ranking`) |
| `POST` | `/api/salas/{sala}/responder` | Regista a resposta de um participante na pergunta ativa e atualiza o ranking |
| `POST` | `/api/salas/{sala}/sair/{participante}` | Marca um participante como desconectado da sala |

## Rotas Web Principais

| Método | Rota | Nome | Descrição |
|--------|------|------|-----------|
| `GET` | `/` | — | Home Page Arena Dark/Gold com entrada rápida por código |
| `GET` | `/jogar` | `game.play` | Modo Individual Show do Milhão (4 Fases) |
| `POST` | `/jogar/responder` | `game.answer` | Submete resposta no modo individual |
| `POST` | `/jogar/ajuda` | `game.lifeline` | Aciona `dica`, `convidado`, `cartas` ou `pulo` no modo individual |
| `POST` | `/jogar/encerrar` | `game.finish` | Permite parar a partida garantindo os pontos acumulados |
| `GET` | `/jogar/resultado` | `game.result` | Resultado final e revisão bíblica da partida |
| `GET` | `/curiosidades` | `curiosidades` | Enciclopédia Bíblica Ilustrada e Regras Clássicas (`regras.wav`) |
| `GET` | `/apresentador` | `apresentador.painel` | Painel de criação e listagem de salas ao vivo |
| `GET` | `/apresentador/control/{sala}` | `apresentador.control` | Estúdio de controle da sala ao vivo |
| `POST` | `/apresentador/control/{sala}/ajuda` | `apresentador.ajuda` | Aciona `dica`, `convidado`, `cartas`, `pulo` ou `revelar` na sala |
| `GET` | `/projecao/{sala}` | `projecao.tela` | Telão de projeção para TV/Projetor da igreja |
| `GET` | `/entrar/{codigo?}` | `entrar.view` | Interface mobile do participante |
| `GET` | `/login` / `/registro` | `login` / `register` | Autenticação de apresentadores, editores e administradores |
| `GET` | `/admin` | `admin.dashboard` | Painel de Administração e exportação CSV/JSON |
