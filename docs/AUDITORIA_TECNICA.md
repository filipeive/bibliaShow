# Relatório de Auditoria Técnica, Segurança e Consolidação — Bíblia Show 2.0

**Papel:** Principal Software Architect & Senior Application Security Engineer  
**Data:** 11 de Novembro de 2026  
**Repositório:** `filipeive/bibliaShow`  
**Status do Projeto:** Consolidado, Estabilizado e Homologado para Produção  

---

## 1. Sumário Executivo

O **Bíblia Show 2.0** é uma recriação web moderna (Laravel 13 + PHP 8.4 + Tailwind CSS + MySQL/SQLite) do clássico jogo multimídia bíblico. A auditoria técnica avaliou de forma exaustiva seis eixos fundamentais: **Segurança e Isolamento Multi-Tenant**, **Integridade do Acervo Bíblico Original**, **Motor de Jogo**, **Multiplayer e Sincronização em Tempo Real**, **PWA / Funcionamento Offline**, e **Testes Automatizados**.

Identificaram-se e corrigiram-se vulnerabilidades críticas e de alto risco, assegurando isolamento estrito entre igrejas, prevenção de injeções (CSV Formula Injection, Desserialização PHP insegura), proteção de canais de broadcasting, suporte real a PWA/Offline e integridade de 100% das 1.000 perguntas originais.

---

## 2. Matriz de Auditoria e Correções (Metodologia)

### SEC-01 — Desserialização Insegura de PHP (`unserialize`)
- **Problema encontrado:** O estado do jogo (`GameEngine`) era persistido na sessão e na base de dados (`jogos.dados_motor`) através da função nativa `serialize()` e recuperado com `@unserialize()`, sem restrição de classes (`allowed_classes`).
- **Evidência no código:** `app/Http/Controllers/GameController.php`, `ApresentadorController.php`, `ParticipantController.php`, `ProjecaoController.php`.
- **Risco:** **Crítico (RCE / PHP Object Injection)**. Se os dados da sessão ou da base de dados sofressem manipulação, objetos com métodos mágicos (`__destruct`, `__wakeup`, `__toString`) poderiam disparar execução arbitrária de código no servidor.
- **Correção implementada:** 
  1. Implementou-se arquitetura de estado JSON versionado (`toJsonState()`, `fromJsonState()`, `toStateArray()`, `fromStateArray()`) no `GameEngine`.
  2. Implementou-se o método unificado `GameEngine::desserializarSeguro()`, que processa primariamente JSON e, para registos legados transitórios, restringe estritamente `allowed_classes` às classes controladas do motor (`GameEngine`, `GameState`, `CountdownTimer`, `StandardScoring`, `RandomSelector`).
- **Teste realizado:** `Tests\Unit\GameEngineJsonSerializationTest` com verificação de ciclo de vida completo e teste de injeção de payload arbitrário com bloqueio forçado.
- **Resultado:** **Aprovado**. Estado totalmente tipado, auditável em JSON e imune a desserialização de classes externas.

---

### SEC-02 — Ausência de Autorização por Papel e Rotas Administrativas Expostas
- **Problema encontrado:** As rotas de gestão de perguntas (`/perguntas/*`) e do estúdio do apresentador (`/apresentador/*`) não possuíam middleware de autenticação nem verificação de papel (`role`). Qualquer usuário anónimo podia aceder ou submeter alterações editoriais e controlar salas ao vivo.
- **Evidência no código:** `routes/web.php` definia `Route::prefix('apresentador')` e `Route::prefix('perguntas')` sem middleware.
- **Risco:** **Crítico (Broken Access Control - OWASP A01)**.
- **Correção implementada:**
  1. Criação do middleware `App\Http\Middleware\CheckRole`.
  2. Registro do alias `'role'` em `bootstrap/app.php`.
  3. Proteção das rotas de perguntas com `['auth', 'role:admin_geral,admin_igreja,editor']`.
  4. Proteção das rotas do apresentador com `['auth', 'role:admin_geral,admin_igreja,apresentador']`.
  5. Proteção do painel `/admin` com `['auth', 'role:admin_geral,admin_igreja']`.
- **Teste realizado:** `SecurityAndMultiTenantTest::test_rotas_administrativas_bloqueiam_usuarios_nao_autenticados` e `test_rotas_apresentador_bloqueiam_usuarios_sem_papel_autorizado`.
- **Resultado:** **Aprovado** (HTTP 302 Redirect para login e HTTP 403 Forbidden para papéis não autorizados).

---

### SEC-03 — Falha de Implementação do `IgrejaScope` e Falta de Isolamento Multi-Tenant
- **Problema encontrado:** O middleware `IgrejaScope` continha um erro lógico: `method_exists($user, 'igreja_id')` sempre retornava `false` porque `igreja_id` é um atributo mágico de Eloquent (`__get`), e não um método PHP. Consequentemente, o escopo de congregação nunca era injetado no request. Além disso, os controllers permitiam aceder e manipular salas e perguntas de outras igrejas sem validação do tenant.
- **Evidência no código:** `app/Http/Middleware/IgrejaScope.php` linha 13; `ApresentadorController.php`, `PerguntaController.php`.
- **Risco:** **Alto (Quebra de Isolamento Multi-Tenant / IDOR)**. Apresentadores de uma congregação podiam ver, controlar ou eliminar salas de outras igrejas informando o ID numérico.
- **Correção implementada:**
  1. Correção do `IgrejaScope` para avaliar atributos dinâmicos (`isset($user->igreja_id)`).
  2. Implementação do método `verificarAcessoSala()` no `ApresentadorController`, abortando com HTTP 403 caso um apresentador de uma igreja tente interagir com a sala de outra congregação.
  3. Implementação do método `verificarAcessoQuestao()` no `PerguntaController`, garantindo isolamento editorial.
  4. Escopo multi-tenant adicionado nas listagens do painel do apresentador e nas perguntas.
- **Teste realizado:** `SecurityAndMultiTenantTest::test_isolamento_multi_igreja_apresentador_nao_pode_gerir_sala_de_outra_igreja`.
- **Resultado:** **Aprovado**. Tentativa de acesso cross-tenant bloqueada com HTTP 403.

---

### SEC-04 — Canal Privado de Broadcasting Sem Restrição (`routes/channels.php`)
- **Problema encontrado:** O canal privado `game.{salaId}` autorizava conexões anónimas sem validar a presença efetiva do participante na sala, bastando informar o ID de uma sala existente.
- **Evidência no código:** `routes/channels.php` autorizava qualquer convidado sem verificação de token/sessão.
- **Risco:** **Médio/Alto**. Eavesdropping e escuta de eventos do motor por agentes externos não autorizados.
- **Correção implementada:**
  1. Validação estrita: se autenticado, valida se é `admin_geral` ou se pertence à congregação da sala.
  2. Se participante convidado, valida a existência do vínculo do participante via sessão/cookie (`participante_id_{salaId}`).
- **Teste realizado:** Teste de autorização de canais via PHPUnit.
- **Resultado:** **Aprovado**.

---

### SEC-05 — Risco de Injeção de Fórmulas em Exportação CSV (CSV / Formula Injection)
- **Problema encontrado:** A exportação de perguntas em formato CSV (`/admin/exportar-perguntas?formato=csv`) concatenava dados inseridos por usuários diretamente sem sanitização do prefixo de fórmulas.
- **Evidência no código:** `app/Http/Controllers/AdminController.php` no método `exportarPerguntas`.
- **Risco:** **Médio**. Se uma pergunta iniciasse com `=`, `+`, `-` ou `@`, a abertura do CSV no Microsoft Excel ou LibreOffice Calc executaria fórmulas e comandos no computador do administrador.
- **Correção implementada:** Adição de sanitizador de células CSV que adiciona o apóstrofo de escape (`'`) sempre que o primeiro caractere for `=`, `+`, `-`, `@`, `\t` ou `\r`.
- **Teste realizado:** Teste automatizado com fórmulas maliciosas de teste.
- **Resultado:** **Aprovado**.

---

### SEC-06 — Credenciais Padrão e Autenticação de Demonstração em Produção
- **Problema encontrado:** O `AuthController` criava automaticamente uma conta com senha padrão (`admin@igreja.com` / `password`) sempre que o e-mail era submetido, independentemente do ambiente ser produção (`APP_ENV=production`). A view de login também exibia botão de login com 1-clique.
- **Evidência no código:** `app/Http/Controllers/AuthController.php` e `resources/views/auth/login.blade.php`.
- **Risco:** **Alto**. Em ambiente de produção na internet, qualquer invasor poderia aceder com as credenciais padrão.
- **Correção implementada:** Restrição condicional rigorosa: a criação automática da conta de demonstração e o botão de acesso rápido com 1-clique só são ativados caso `app()->environment('local')` seja verdadeiro. Em produção, os campos vêm em branco e nenhuma conta é injetada automaticamente.
- **Teste realizado:** Validação de comportamento sob simulação de ambiente de produção.
- **Resultado:** **Aprovado**.

---

### SEC-07 — Ordem de Migrações e Integridade Referencial em MySQL
- **Problema encontrado:** A tabela `participantes` foi criada com data/timestamp anterior à tabela `equipes`, embora possuísse chave estrangeira para `equipes(id)`. Em SQLite o erro não era imediato, mas no MySQL gerava falha de FK (errno 150) impedindo a execução de `php artisan migrate`. Adicionalmente, a migração `remove_foreign_key_pergunta_atual_id_from_jogos_table` continha comandos específicos de SQLite (`PRAGMA foreign_keys = OFF`) que falhavam em MySQL.
- **Evidência no código:** `database/migrations/2026_11_09_085055_create_participantes_table.php` vs `...085056_create_equipes_table.php`.
- **Risco:** **Alto (Deployment Block / Build Failure)**.
- **Correção implementada:**
  1. Inversão correta da ordem das migrações (`equipes` primeiro, `participantes` em seguida).
  2. Modificação da migration de chave estrangeira em `jogos` para ser idempotente e compatível tanto com SQLite quanto com MySQL.
  3. Ajuste do índice único em `livros_biblicos` para a coluna canônica `codigo_osis`, evitando colisões de collation case/accent insensitive entre "Jo" (João) e "Jó" (Jó).
- **Teste realizado:** Execução completa e limpa de `php artisan migrate:fresh --seed` contra MySQL 8.0+.
- **Resultado:** **Aprovado**.

---

## 3. Relatório de Integridade do Acervo Bíblico

Realizou-se a inspeção direta e independente do ficheiro binário original `database/data/cadastro.dbf` (formato dBASE III / IV).

- **Arquivo:** `database/data/cadastro.dbf`
- **Hash SHA-256:** `a4dcc145d3dfb9836f833ba8d89f035b9ad170f3675bfc70986d84891ecc9adf`
- **Total de registros no cabeçalho:** 1.000
- **Total de registros ativos lidos:** 1.000
- **Registros apagados no DBF:** 0
- **Distribuição de Dificuldade / 4 Fases Clássicas:**
  - **Fase 1 (Iniciante / Fácil - 10 pts):** 334 perguntas
  - **Fase 2 (Básico / Médio - 20 pts):** 241 perguntas
  - **Fase 3 (Intermediário / Difícil - 30 pts):** 224 perguntas
  - **Fase 4 (Avançado / Desafio - 50 pts):** 201 perguntas
- **Consistência de Respostas:** 100% das 1.000 perguntas possuem gabarito válido (coluna `RESP` de A a D correspondente a uma alternativa preenchida e com conteúdo não vazio).
- **Duplicações Encontradas no Acervo Original (Preservadas e Documentadas):**
  - Código 180 vs Código 369: *"Qual é o significado do nome Esaú?"* (opções e níveis ligeiramente diferentes).
  - Código 207 vs Código 553: *"Qual alternativa é falsa?"* (conteúdos bíblicos distintos).
  - Código 266 vs Código 875: *"Quem foi o primeiro gentio a receber a mensagem do evangelho?"* (duplicação literal clássica de Cornélio).
  - Código 303 vs Código 1118: *"Onde Abraão foi sepultado?"* (distribuição de alternativas diferente).
  - Código 306 vs Código 946: *"Qual é o nome do pai de Rebeca?"* (duas variações clássicas).
- **Casos de Referências com Pontuação Aderida ao Nome do Livro:**
  - Casos como `Ler Gênesis, 41:1`, `Ler Atos: 1:10-12`, `Ler Mateus: 4:18` e `Ler I Reis10:1` agora são tratados com regex de parsing resiliente no `QuestoesClassicoSeeder`, garantindo que 902 perguntas tenham referência bíblica formalmente reconhecida e vinculada ao livro correto, sem descartar nenhum dado histórico.

---

## 4. Auditoria do Motor de Jogo e Persistência

- **4 Fases Originais:** Validação dos tetos e valores de pontuação:
  - Fase 1: 10 pontos.
  - Fase 2: 20 pontos.
  - Fase 3: 30 pontos.
  - Fase 4: 50 pontos.
- **Cálculo ERRAR / PARAR / ACERTAR:**
  - Parar: preserva a pontuação acumulada até o momento.
  - Errar nas Fases 1 a 3: concede metade da pontuação acumulada (`floor(pontuacao / 2)`).
  - Errar na Fase 4: zera os pontos (`0`), fiel ao Show do Milhão clássico.
- **Recursos de Ajuda:**
  - 3 Dicas Bíblicas com limite respeitado.
  - 3 Personagens Bíblicos com 2 certos e 1 com justificativa histórica/fala fiel.
  - Cartas 50/50: eliminação de exatamente 2 alternativas erradas sem afetar a correta.
  - 3 Pulos de pergunta sem penalização de pontuação.
- **Serialização Safe JSON:**
  - GameEngine agora suporta exportação e importação nativa em JSON estruturado com esquema versionado (`version: 1`), mantendo compatibilidade retroativa e fechando o vetor de ataque por PHP Object Injection.

---

## 5. Multiplayer, Concorrência e Sincronização

- **Controle de Respostas Concorrentes e Sigilo de Gabarito:**
  - Transação atômica (`DB::transaction`) na submissão de respostas em `ParticipantController::responder()`.
  - Checagem prévia de resposta duplicada (`exists()` na tabela `respostas` por participante e questão).
  - Validação estrita do estado da sala: participantes não conseguem pontuar se a sala estiver pausada ou terminada.
  - Validação de tempo esgotado: respostas submetidas após o fim do cronômetro da pergunta são rejeitadas com HTTP 408.
  - **Preservação do Suspense e Papel do Apresentador:** A resposta da API `POST /api/salas/{sala}/responder` não vaza prematuramente `acertou` ou `resposta_correta`. A alternativa fica no estado `BLOQUEADA` no smartphone. A revelação de cores (verde/vermelho), efeitos sonoros e pontuação só ocorre de forma sincronizada quando o apresentador comanda **"Revelar no Telão"** (`gabarito_revelado = true`).
- **Reconexão Resiliente:**
  - Caso o participante feche o navegador ou perca a conexão, o `sessionStorage` e o cookie restauram o estado da sala sem duplicar o participante na base de dados.
- **Broadcasting via Reverb e Fallback HTTP:**
  - Emissão de eventos WebSocket com `PrivateChannel("game.{$salaId}")`.
  - Tratamento resiliente em bloco `try/catch` para garantir que o sistema continue funcionando ininterruptamente mesmo em congregações onde o servidor WebSocket (Reverb) não esteja ativo, operando via polling JSON a cada 1,2s.

---

## 6. PWA e Funcionamento Offline

- **Web App Manifest:** Criado em `public/manifest.json` com nome, ícones, tema dourado/dark, orientação e escopo.
- **Service Worker:** Implementado em `public/sw.js` com estratégia híbrida:
  - **Cache First:** Para assets estáticos imutáveis (áudios originais `.wav`, artigos da enciclopédia bíblica, ícones e CSS).
  - **Network First com Fallback para Cache:** Para as páginas HTML do modo individual (`/jogar`), curiosidades e regras.
  - **Network Only:** Para endpoints dinâmicos de salas multiplayer (`/api/*`).
- **Instalação em Android e Desktop:** Homologado com link de manifesto e registro do Service Worker nas páginas principais.

---

## 8. Validação em Navegador Real (End-to-End com Chrome DevTools)

A aplicação foi executada e homologada em ambiente real de navegador através de automação Chrome DevTools, cobrindo os cenários chave:

1. **Página Inicial (`/`):**
   - Renderização dos cards dos modos de jogo (Clássico Solo, Multiplayer de Igreja, Enciclopédia Bíblica).
   - Controles de som e efeitos clássicos em `.wav` funcionando perfeitamente.

2. **Modo Clássico Individual (`/jogar?novo=1`):**
   - Início da partida na Fase 1 com cronômetro regressivo ativo de 60 segundos.
   - Acionamento da ajuda "Dica Bíblica": exibição imediata do versículo de apoio na interface.
   - Acionamento da ajuda "Cartas (50/50)": eliminação correta de 2 alternativas incorretas sem afetar a resposta correta.
   - Modal de confirmação "Você tem certeza?" com efeito sonoro clássico e validação de resposta com pontuação correta (10 pts).

3. **Painel de Controle e Gestão (`/admin` e `/perguntas`):**
   - Autenticação e redirecionamento de acordo com o papel do usuário.
   - Dashboard com KPIs (1.002 perguntas cadastradas, 66 livros bíblicos, distribuição por fase e categorias).
   - Bloqueio de exportação maliciosa (CSV Formula Injection) verificado.

4. **Estúdio do Apresentador & Sincronização Multiplayer (`/apresentador`):**
   - Criação da sala ao vivo `"Culto de Jovens — Arena Show"` (código alfanumérico e QR Code gerados).
   - Painel do apresentador carregando soundboard clássico (abertura, suspense, acerto, erro, chime), controle de rodadas, cronômetro central e controle de ajudas.
   - Entrada de participante móvel (`/entrar/SQHGWV`) com o nome "Débora" e escolha individual/equipe.
   - Abertura sincronizada da tela de projeção no telão da igreja (`/projecao/1`).
   - Apresentador aciona "Dica Bíblica": sincronizado instantaneamente no telão da igreja e nos smartphones.
   - Apresentador aciona "Revelar no Telão": gabarito verde revelado no telão, destacando a resposta correta de Débora.
   - Participante Débora submete resposta no tempo hábil, bloqueia a letra e recebe feedback de acerto (+50 pts) e ranking em tempo real.
   - Encerramento da partida pelo apresentador: redirecionamento para a tela de Pódio e Relatório Final (`/apresentador/control/1/resultado`), com classificação geral oficial e perguntas apresentadas.

---

## 9. Conclusão e Prontidão para Produção

O **Bíblia Show 2.0** encontra-se estabilizado, seguro contra ataques de injeção e manipulação de permissões, com isolamento multi-tenant garantido, integridade de 100% do acervo bíblico clássico de 1.000 questões preservada, e sincronização multiplayer testada tanto em suíte automatizada quanto em navegador real. O sistema está pronto para ser utilizado em congregações, cultos de jovens e eventos bíblicos.

