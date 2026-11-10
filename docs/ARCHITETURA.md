# Arquitetura — Bíblia Show 2.1

Resumo executivo da arquitetura do produto **Bíblia Show 2.1 (Clássico Recriado)**.

## Estado Atual (100% Implementado)

- ✅ **Motor de Jogo Clássico Show do Milhão** (`App\Game\Engine\GameEngine`): 4 Fases (10, 20, 30 e 50 pts), cronômetro de 60s, placar `ERRAR | PARAR | ACERTAR`, 3 Dicas Bíblicas, 3 Convidados Bíblicos, Cartas 50/50, 3 Pulos e revelação de gabarito.
- ✅ **Acervo Original Restaurado**: 1.000 perguntas importadas de `database/data/cadastro.dbf` (`QuestoesClassicoSeeder`), 66 livros bíblicos, 25 trilhas e locuções originais em `.wav` (`public/audio/classico/`) e 10 artigos ilustrados da Enciclopédia Bíblica (`public/data/enciclopedia.json`).
- ✅ **Modo Individual Offline** (`/jogar`): funciona sem necessidade de conta ou internet.
- ✅ **Estúdio do Apresentador & Salas Ao Vivo** (`/apresentador`, `/apresentador/control/{sala}`): criação de salas, código de 6 caracteres, QR Code, criação de equipes, acionamento de ajudas clássicas e revelação do gabarito para o telão.
- ✅ **Telão de Projeção** (`/projecao/{sala}`): interface de transmissão sincronizada com áudio clássico, dicas, convidados, corte 50/50 e ranking em tempo real.
- ✅ **Smartphone do Participante** (`/entrar/{codigo}`): entrada rápida sem conta, confirmação *"Você tem certeza?"* e feedback bíblico imediato.
- ✅ **Autenticação & Painel de Administração** (`/login`, `/registro`, `/admin`): gestão global de questões, aprovação editorial, igrejas, usuários, reimportação 1-clique do clássico e exportação CSV/JSON.

## Stack Tecnológico

| Camada | Tecnologia | Status |
|--------|-----------|--------|
| Backend | Laravel 13 + PHP 8.4 | Implementado |
| Frontend | Blade + Tailwind CSS + SVG Icons | Implementado |
| Banco de Dados | SQLite (padrão) / MySQL / PostgreSQL | Implementado |
| Tempo Real | Laravel Reverb + Polling JSON (`/api/salas/{id}`) | Implementado |
| Áudio Clássico | HTML5 Audio (`public/audio/classico/*.wav`) | Implementado |
| Autenticação | Session Auth (`App\Models\Usuario`) | Implementado |
