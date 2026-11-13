# Bíblia Show 2.0 — Auditoria de Consolidação Multiplataforma

Data da auditoria: 2026-11-13  
Escopo: Backend Laravel, Web/PWA, Android Flutter, Desktop Electron, Arena Multiplayer e Ambiente de Produção (`146.235.224.99`).

---

## 1. Resumo Executivo

A auditoria identificou e corrigiu vulnerabilidades de autorização e vazamento de gabarito em tempo real, alinhou regras divergentes entre os motores Laravel e Flutter (com especificação comum em [GAME_RULES_SPEC.md](file:///home/fdev-ms/Filipe/bibliaShow/docs/GAME_RULES_SPEC.md)), corrigiu o Service Worker para subdiretório `/bibliashow/` sem cache de páginas autenticadas, corrigiu URLs/menus do Electron e normalização de URL no app Flutter.

> **Importante:** Este documento distingue rigorosamente o que foi **implementado**, **testado automaticamente**, **testado em dispositivo real** e **ainda não validado**.

---

## 2. Matriz de Classificação Rigorosa

| Área / Funcionalidade | Estado de Implementação | Testado Automaticamente | Dispositivo Real | Ainda Não Validado |
| :--- | :--- | :--- | :--- | :--- |
| **Auto Tela Cheia + Definições** (`navbar.blade.php`, `projecao/tela.blade.php`) | Implementado (`localStorage.biblia_show_auto_fullscreen`, default `on`, botão manual no modal e Telão) | Não aplicável (UI/JS browser) | Pendente | Validação visual em Chrome/Edge/Mobile Android |
| **Segurança `routes/channels.php`** | Implementado (removido acesso anónimo `espectador`/`projecao`; exige auth da igreja/admin ou sessão válida de participante na sala) | Indiretamente (suite Laravel) | — | Handshake WebSocket real com Laravel Reverb |
| **Sigilo do Gabarito em Broadcasts** (`PerguntaIniciada`, `JogoEstadoAlterado`, `RespostaEnviada`) | Implementado (sanitização de `resposta_correta`, `referencia_biblica`, `dica`, `ajudas`, `explicacao`, `correta`, `pontos`) | Sim (`SecurityAndMultiTenantTest::test_eventos_broadcast_sanitizam_gabarito`) | — | Tráfego WebSocket real em produção |
| **Autorização Participante & Equipas** (`ParticipantController.php`, Web + Flutter) | Implementado (`equipe_id` validado na sala, `X-Participante-Codigo` com `hash_equals`, reconexão retorna `pontuacao_total`) | Sim (`SecurityAndMultiTenantTest`, `MultiplayerFlowTest`) | Pendente | — |
| **Isolamento Multi-Igreja em Questões** (`PerguntaController.php`) | Implementado (`igreja_id` forçado em `store`/`update` para não-`admin_geral`, bloqueio de escrita em outras igrejas) | Sim (`SecurityAndMultiTenantTest::test_editor_nao_pode_editar_questao_global_nem_de_outra_igreja`) | — | — |
| **Consistência de Regras Laravel ↔ Flutter** (`GameEngine.php` vs `GameEngineService.dart`) | Implementado (`valorAoErrar = 0` na Fase 4, limite de 1 dica/convidado por pergunta, penalidade ao perder por 3 erros/tempos, `GAME_RULES_SPEC.md`) | Sim (`mobile/test/game_engine_and_assets_test.dart`, PHPUnit) | Pendente | Execução completa de uma partida de 24 perguntas em Android físico |
| **Sincronização da Arena (HTTP Polling)** | Implementado (Laravel API + Web `entrar.blade.php` + Telão + Flutter `ApiService`) | Sim (`MultiplayerFlowTest::test_fluxo_completo_arena_com_equipa_ranking_e_reconnexao`) | Pendente | Teste simultâneo com múltiplos smartphones + Telão ao vivo |
| **PWA & Cache em Subdiretório (`/bibliashow/`)** (`public/sw.js`, `public/manifest.json`) | Implementado (`self.registration.scope`, bloqueio explícito de rotas privadas, cache apenas de rotas públicas) | Não automatizado (JS Service Worker) | Pendente | Instalação PWA em Android Chrome sob `/bibliashow/` e teste offline no browser |
| **Jogo Individual Offline (Web vs Flutter)** | **Flutter:** 100% offline com `assets/data/questoes.json` e SQLite. **Web (`/jogar`):** SSR Laravel (requer servidor para `POST /jogar/responder`) | **Flutter:** Parsing JSON, áudios e motor testados em VM (`flutter test`). **Web:** PHPUnit | Pendente | SQLite nativo (`sqflite`) e reprodução real de áudio no Android |
| **Aplicação Desktop Electron** (`electron/main.js`) | Implementado (corrigidos menus `/jogar`, `/apresentador`, `/projecao/1` e fallback `https://146.235.224.99/bibliashow`) | Não automatizado | Não testado | Execução do binário Electron após correção dos menus |
| **Produção (`146.235.224.99`)** | Deploy ativo (`APP_ENV=production`, `APP_DEBUG=false`, `vendor/` sem pacotes dev, Nginx `/bibliashow`, MySQL, PHP-FPM) | Verificado via SSH & `curl` | — | **Laravel Reverb não está em execução** (`BROADCAST_CONNECTION=log`, `NO_REVERB_PROCESS`) |

---

## 3. Correções Realizadas na Auditoria

### 3.1. Segurança & Multi-Tenant
1. **`routes/channels.php`:** Removida a autorização aberta de `espectador`/`projecao` para o canal privado `game.{salaId}`. Agora apenas o apresentador, utilizador da mesma `igreja_id`, `admin_geral`, ou participante autenticado na sessão da sala pode subscrever.
2. **Eventos Broadcast:** `PerguntaIniciada`, `JogoEstadoAlterado` e `RespostaEnviada` agora sanitizam os dados sensíveis antes da transmissão em tempo real.
3. **`ParticipantController`:**
   - `entrar()` valida que `equipe_id` pertence à `$sala->id` (`422` se inválida) e devolve `'pontuacao_total'` na reconexão.
   - Adicionado `autorizarParticipante()` validando `sala_id` e `X-Participante-Codigo` (`hash_equals`), usado em `responder()`, `sair()`, `usarAjuda()` e `juntarEquipe()`.
   - Clientes Web (`entrar.blade.php`) e Flutter (`ApiService`) atualizados para enviar `X-Participante-Codigo`.
4. **`PerguntaController`:** `store()` e `update()` impõem `igreja_id` do utilizador autenticado quando não é `admin_geral`, e `verificarAcessoQuestao(..., true)` impede edição/exclusão cruzada entre igrejas.

### 3.2. Consistência de Regras (`GameEngine` Laravel vs `GameEngineService` Flutter)
- Corrigida divergência crítica no Flutter (`mobile/lib/services/game_engine_service.dart`): `valorAoErrar` agora retorna `0` na **Fase 4** (`faseAtual >= 4`), alinhado com `GameEngine::getPlacarMilhao()` do Laravel.
- Alinhado limite de uso de **Dica Bíblica** e **Convidado Bíblico** para no máximo **1 vez por pergunta** no Flutter.
- Aplicada penalidade `valorAoErrar` quando a partida Flutter termina por 3 erros ou 3 tempos esgotados (`aplicarPenalidadeErro: true`).
- Nota arquitetural documentada em `docs/GAME_RULES_SPEC.md`: o motor Laravel usa `StandardScoring` (`100 + bónus tempo + bónus sequência`) para ranking web enquanto exibe o Placar do Milhão (`10/20/30/50`), ao passo que o motor offline Flutter acumula diretamente os valores do Placar do Milhão (`10/20/30/50`).

### 3.3. PWA, Electron e Flutter
- **`public/sw.js` e `public/manifest.json`:** Escopo relativo `./` / `self.registration.scope` para funcionar em `/bibliashow/`. Bloqueio estrito (`PRIVATE_PREFIXES`) para não armazenar em cache páginas autenticadas (`/admin`, `/apresentador`, `/perguntas`, `/api`, `/projecao`, `/entrar`, etc.).
- **`electron/main.js`:** Corrigidas rotas dos menus (`/jogar`, `/apresentador`, `/projecao/1`) e atualização de `activeUrl` no fallback para `https://146.235.224.99/bibliashow`.
- **`mobile/lib/services/api_service.dart`:** Corrigido bug em `normalizarUrl()` que gerava barra dupla (`//bibliashow`) quando a URL tinha `/` final, definido default HTTPS, e adicionado envio de `X-Participante-Codigo`.

---

## 4. Limitações Encontradas e Itens Pendentes para Validação Real

1. **Laravel Reverb em Produção:** No servidor `146.235.224.99`, `BROADCAST_CONNECTION=log` e nenhum processo `reverb:start` ou `supervisor` está ativo. A Arena funciona em produção via **polling HTTP** (1.5s–2s). Caso se pretenda WebSockets ativos em produção, será necessário configurar `BROADCAST_CONNECTION=reverb` e um daemon Systemd/Supervisor para `php artisan reverb:start` e `queue:work`.
2. **Testes Flutter em VM Linux vs Android Real:** A suite `flutter test` (`9` testes) validou regras do motor, JSON das questões, áudios em disco e normalização de URL na VM. Contudo, `sqflite` (SQLite) e `audioplayers` nativo dependem de canais nativos Android e **não foram executados em dispositivo Android físico nesta sessão**.
3. **Electron:** Código corrigido e inspecionado, porém o executável desktop não foi recompilado nem executado manualmente após a alteração de `electron/main.js`.
