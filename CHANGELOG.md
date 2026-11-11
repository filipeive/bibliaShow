# Changelog — Bíblia Show

Todas as alterações relevantes do projeto **Bíblia Show** estão documentadas neste arquivo.

## [2.2.0] — 2026-11-11 (Auditoria Técnica, Segurança e Consolidação)

### Segurança e Autorização
- **Prevenção de PHP Object Injection**: Substituição da desserialização nativa com `unserialize()` por persistência de estado em JSON versionado (`GameEngine::toJsonState()` / `fromJsonState()`), além de whitelist estrita de classes em caso de fallback legado.
- **Controle de Acesso por Papel (RBAC)**: Criação do middleware `CheckRole` registado em `bootstrap/app.php` protegendo rotas `/admin/*` (`admin_geral`, `admin_igreja`), `/apresentador/*` (`admin_geral`, `admin_igreja`, `apresentador`) e `/perguntas/*` (`admin_geral`, `admin_igreja`, `editor`).
- **Isolamento Efetivo Multi-Tenant**: Correção do `IgrejaScope` e validação nos controllers de que salas e questões pertencem estritamente à mesma congregação do usuário autenticado (retornando HTTP 403 em tentativas de IDOR cross-tenant).
- **Proteção de Canais Privados de Broadcasting**: Autorização estrita em `routes/channels.php` para `game.{salaId}`, permitindo assinatura somente de apresentadores/administradores da mesma igreja ou de participantes devidamente vinculados à sala.
- **Sanitização contra CSV Formula Injection**: Sanitização automática de caracteres maliciosos (`=`, `+`, `-`, `@`, `\t`) nas exportações de acervo no painel administrativo.
- **Ambiente e Demonstração**: Acesso rápido com botão de auto-login e criação de conta admin padrão restritos a ambiente local (`APP_ENV=local`).

### Acervo Bíblico e Banco de Dados
- **Auditoria de Integridade**: Verificação independente com hash SHA-256 de `database/data/cadastro.dbf` confirmando 1.000 perguntas originais ativas e 100% de consistência de gabaritos (`A` a `D`).
- **Parsing Resiliente de Referências Bíblicas**: Tratamento de referências com pontuação aderida no `QuestoesClassicoSeeder`, elevando para 902 o número de perguntas com referência formal mapeada para os 66 livros canônicos.
- **Correção de Migrações**: Inversão da ordem de criação de `equipes` e `participantes` para respeitar chaves estrangeiras no MySQL; ajuste de unicidade em `livros_biblicos` para `codigo_osis` evitando colisão de collation insensível a acentos entre "Jo" (João) e "Jó" (Jó).

### PWA e Funcionamento Offline
- **Web App Manifest**: Criação de `public/manifest.json` com especificações de PWA, tema dark/ouro e ícones.
- **Service Worker Inteligente**: Criação de `public/sw.js` com estratégia híbrida (Cache-First para áudios `.wav` e artigos da enciclopédia; Network-First para páginas solo; Network-Only para endpoints dinâmicos).
- **Rotas Dedicadas**: Adição de rotas explícitas para `/manifest.json` e `/sw.js` em `routes/web.php`.

### Multiplayer e Concorrência
- **Atomicidade e Anti-Trapaça**: Submissão de respostas em transação de banco com checagem contra duplicidades e rejeição com HTTP 408 para respostas enviadas após o encerramento do cronômetro.
- **Correção de Sintaxe JS**: Remoção de declaração duplicada da variável `placar` no polling do smartphone participante.
- **Testes End-to-End**: Testes completos no navegador (Chrome DevTools MCP) e suíte automatizada expandida para 23 testes (80 asserções).

---

## [2.1.0] — 2026-11-10

### Adicionado
- **Painel de Administração (`/admin`) e Autenticação (`/login`, `/registro`, `/logout`)**:
  - Autenticação completa com o modelo `Usuario` (`admin_geral`, `admin_igreja`, `apresentador`, `editor`) e botão de acesso rápido em 1 clique para demonstração (`admin@igreja.com` / `password`).
  - Dashboard administrativo com KPIs em tempo real (questões totais/aprovadas/pendentes, 66 livros, 366 capítulos, salas ativas, participantes, respostas, igrejas e usuários).
  - Distribuição de questões pelas 4 Fases do jogo, fila editorial de aprovação rápida, gestão de usuários e congregações, sincronização 1-clique do acervo clássico (`cadastro.dbf`) e exportação de perguntas em CSV/JSON.
- **Mecânica Clássica Completa nas Salas Ao Vivo, Apresentador, Projeção e Smartphones**:
  - Seleção automática de perguntas distribuídas pelas **4 Fases Clássicas** (10, 20, 30 e 50 pontos) nas salas multiplayer.
  - Controles no Estúdio do Apresentador (`/apresentador/control/{sala}`) para acionar **Dica Bíblica**, **Cartas 50/50**, **Convidados Bíblicos**, **Pular Pergunta** e **Revelar Gabarito no Telão**, além de mesa de áudio clássico (`Abertura`, `Suspense`, locuções).
  - Telão de Projeção (`/projecao/{sala}`) sincronizado com exibição dinâmica de dicas, falas dos convidados bíblicos, corte de alternativas (`50/50`), destaque verde ao revelar o gabarito, QR Code integrado e efeitos sonoros.
  - Tela do Participante (`/entrar/{codigo}`) com modal *"Você tem certeza?"*, bloqueio automático de alternativas eliminadas por `Cartas 50/50`, visualização de dicas/convidados ativos e sons clássicos.
- **Nova Home Page (`welcome.blade.php`) & Navbar Responsiva Sem Quebra (`partials/navbar.blade.php`)**:
  - Redesign completo inspirado no layout Stitch Dark/Gold Arena com preview interativo de questão clássica, placar `ERRAR | PARAR | ACERTAR`, entrada rápida por código de sala, vitrine das 4 fases e cards ilustrados da Enciclopédia Bíblica.
  - Componente global `partials/navbar.blade.php` com `whitespace-nowrap` e drawer mobile para eliminar qualquer quebra de linha em telas médias e grandes.

---

## [2.0.0] — 2026-11-10

### Adicionado
- **Importação das 1.000 Perguntas Clássicas (`cadastro.dbf`)**:
  - Comando `php artisan biblia:importar-classico` e `QuestoesClassicoSeeder` importando 1.000 perguntas originais, 66 livros bíblicos completos (incluindo *Lamentações* e *Obadias*), referências bíblicas e ajudas dos 3 convidados bíblicos por questão.
- **Extração dos 25 Sons Originais (`.wav`) e Enciclopédia Ilustrada**:
  - Conversão direta dos recursos `sndH`/`sndS` dos arquivos `.dxr` do Macromedia Director para `public/audio/classico/*.wav`.
  - Conversão dos 10 artigos `.rtf` e imagens `.bmp` de `Biblia/Dados/` para `public/data/enciclopedia.json`, `public/images/classico/*.jpg` e página `/curiosidades`.
- **Motor Show do Milhão Bíblico (`GameEngine`)**:
  - 4 Fases (10, 20, 30 e 50 pts), cronômetro de 60s, placar `getPlacarMilhao()`, `usarDica()`, `consultarConvidado()`, `usarCartas()`, `usarPulo()` e ícones SVG em todas as views.
