# Bíblia Show 2.0 — Clássico Recriado (Show do Milhão Bíblico)

Recriação web moderna (Laravel 13 + PHP 8.4 + Tailwind CSS) do clássico jogo **Bíblia Show**, preservando e restaurando **100% do acervo original**: as **1.000 perguntas clássicas**, referências bíblicas, ajudas dos personagens bíblicos, **25 trilhas e locuções originais em `.wav`**, a **Enciclopédia Bíblica Ilustrada** (10 artigos e imagens históricas) e adicionando **Salas Multiplayer ao Vivo com QR Code**, **Estúdio do Apresentador**, **Telão de Projeção** e **Painel de Administração**.

---

## Principais Funcionalidades

### 1. Modo Clássico Individual (Estilo Show do Milhão)
- **4 Fases Originais**:
  - **1ª Fase**: 6 perguntas fáceis (**10 pontos** por acerto)
  - **2ª Fase**: 6 perguntas médias (**20 pontos** por acerto)
  - **3ª Fase**: 6 perguntas difíceis (**30 pontos** por acerto)
  - **4ª Fase (Desafio)**: 6 perguntas finais (**50 pontos** por acerto)
- **Placar `ERRAR | PARAR | ACERTAR`**: cálculo fiel de pontuação ao parar ou errar (metade dos pontos nas Fases 1 a 3; perde tudo na 4ª Fase).
- **Modal *"Você tem certeza?"*** antes de confirmar a alternativa escolhida.
- **Cronômetro de 60 segundos** por pergunta.
- **4 Recursos de Ajuda Clássicos**:
  - **3 Dicas Bíblicas**: revela o livro, capítulo e versículo da resposta (`Referência Bíblica: Ler ...`).
  - **3 Convidados Bíblicos**: consulta um dos 3 personagens bíblicos vinculados à questão (2 sabem a resposta certa e 1 explica historicamente por que não vivenciou o fato).
  - **Cartas 50/50**: elimina duas alternativas incorretas.
  - **3 Pulos**: substitui a pergunta atual por outra da mesma fase.

### 2. Áudio Original Extraído do Clássico (`public/audio/classico/`)
- **25 arquivos `.wav`** extraídos dos binários `.dxr` originais do Macromedia Director:
  - Trilhas: `abertura.wav`, `suspense.wav`, `ranking.wav`, `inicio.wav`
  - Locuções de Fase: `fase1.wav`, `fase2.wav`, `fase3.wav`, `fase4.wav`
  - Locuções de Acerto, Erro e Fim: `certa1..3.wav`, `errou1..3.wav`, `fim1..3.wav`
  - Ajudas e Regras: `dica.wav`, `ajuda.wav`, `sabe1..3.wav`, `regras.wav`, `tecla.wav`, `hover.wav`

### 3. Salas Multiplayer Ao Vivo, Estúdio do Apresentador & Telão de Projeção
- **Estúdio do Apresentador (`/apresentador`)**:
  - Criação de salas com código de 6 caracteres e QR Code automático.
  - Controle das 4 fases, pausa/retomada, avanço de perguntas e criação de equipes/células.
  - Acionamento de **Dica Bíblica**, **Cartas 50/50**, **Convidados Bíblicos** e **Revelação do Gabarito** diretamente para o Telão e para os smartphones dos participantes.
  - Mesa de som integrada no estúdio (`Abertura`, `Suspense`, efeitos de acerto/erro).
- **Telão de Projeção (`/projecao/{sala}`)**:
  - Interface limpa de TV/Projetor sem controles administrativos, sincronizada em tempo real.
- **Smartphone do Participante (`/entrar/{codigo}`)**:
  - Entrada sem necessidade de conta, confirmação *"Você tem certeza?"*, atualização automática de alternativas eliminadas (`50/50`), dicas reveladas e ranking ao vivo.

### 4. Enciclopédia Bíblica Ilustrada & Regras (`/curiosidades`)
- **10 estudos históricos ilustrados** extraídos dos arquivos `.rtf` e `.bmp` originais (`Você Sabia?`, `Manuscritos do Mar Morto`, `A Septuaginta`, `Traduções em Português`, `Livros do Antigo e Novo Testamento`, `Glossário Bíblico`, etc.) + locução em áudio das regras (`regras.wav`).

### 5. Autenticação & Painel de Administração (`/admin`, `/login`)
- Autenticação de usuários multi-igreja (`admin_geral`, `admin_igreja`, `apresentador`, `editor`).
- Painel Administrativo (`/admin`) com KPIs em tempo real, distribuição das 1.000+ perguntas pelas 4 fases, fila editorial de revisão, gestão de salas, cadastro de igrejas/usuários, sincronização em 1 clique com `database/data/cadastro.dbf` e exportação CSV/JSON.

---

## Como Executar o Projeto

```bash
cd biblia-show

# 1. Instalar dependências
composer install

# 2. Configurar ambiente (.env)
cp .env.example .env
php artisan key:generate

# 3. Executar migrações e importar as 1.000 perguntas clássicas
php artisan migrate --seed
php artisan biblia:importar-classico

# 4. Iniciar servidor local
php artisan serve
```

### Credenciais Padrão (Painel Admin)
- **URL**: `http://127.0.0.1:8000/login` (possui botão de acesso rápido em 1 clique)
- **E-mail**: `admin@igreja.com`
- **Palavra-passe**: `password`

---

## Documentação Adicional

- [`PROJECT_STATUS.md`](PROJECT_STATUS.md) — Status atual do projeto e histórico de correções
- [`docs/AUDITORIA_TECNICA.md`](docs/AUDITORIA_TECNICA.md) — Relatório aprofundado de auditoria de segurança, integridade do acervo e testes
- [`CHANGELOG.md`](CHANGELOG.md) — Histórico detalhado de versões e melhorias
- [`docs/ARCHITETURA.md`](docs/ARCHITETURA.md) — Arquitetura do motor, módulos e fluxos em tempo real
- [`docs/MODELO_DE_DADOS.md`](docs/MODELO_DE_DADOS.md) — Esquema das tabelas SQLite/MySQL
- [`docs/API.md`](docs/API.md) — Endpoints REST e canais de eventos
- [`docs/INSTALACAO.md`](docs/INSTALACAO.md) — Guia passo a passo de instalação
- [`docs/TESTES.md`](docs/TESTES.md) — Suíte de testes unitários e verificação de rotas
