# Bíblia Show 2.0 — Status do Projeto

**Última Atualização:** 11 de Novembro de 2026  
**Versão Atual:** 2.1.0-estabilizada  
**Status da Auditoria:** Concluída com Sucesso  

---

## 1. Visão Geral do Sistema

O **Bíblia Show 2.0** é a recriação fiel e moderna do clássico jogo multimídia bíblico estilo Show do Milhão, construído com Laravel 13, PHP 8.4, Tailwind CSS, MySQL e SQLite. O projeto integra:
1. **Modo Clássico Individual** (4 Fases, 1.000 perguntas, 25 efeitos sonoros `.wav` clássicos, cronômetro de 60s, ajudas e placar ERRAR/PARAR/ACERTAR).
2. **Estúdio do Apresentador** com criação de salas por QR Code e código alfanumérico de 6 caracteres.
3. **Telão de Projeção** limpo para projetores de congregações com ranking ao vivo e revelação sincronizada de gabarito.
4. **Smartphone do Participante** sem necessidade de cadastro, com suporte a equipes/células e feedback imediato.
5. **Enciclopédia Bíblica Ilustrada** e Regras Originais.
6. **Painel Administrativo Multi-Tenant** com controle de papéis (`admin_geral`, `admin_igreja`, `apresentador`, `editor`).

---

## 2. Resumo das Correções da Auditoria (11/11/2026)

| ID | Área | Classificação | Descrição da Correção | Status |
|---|---|---|---|---|
| **SEC-01** | Segurança | Crítico | Substituição de `unserialize()` livre por estado JSON versionado no `GameEngine` e bloqueio estrito de classes arbitrárias. | Resolvido |
| **SEC-02** | Autorização | Crítico | Criação do middleware `CheckRole` e proteção de rotas `/perguntas/*`, `/apresentador/*` e `/admin/*`. | Resolvido |
| **SEC-03** | Multi-Tenancy | Alto | Correção do `IgrejaScope` e validação estrita de tenant nos controllers do apresentador e perguntas. | Resolvido |
| **SEC-04** | Broadcasting | Alto | Restrição do canal privado `game.{salaId}` para usuários autenticados da igreja e participantes vinculados à sala. | Resolvido |
| **SEC-05** | Segurança | Médio | Sanitização de prefixos de fórmulas (`=`, `+`, `-`, `@`) na exportação de perguntas em CSV. | Resolvido |
| **SEC-06** | Segurança | Alto | Restrição de credenciais padrão e botão de login rápido apenas para `APP_ENV=local`. | Resolvido |
| **SEC-07** | Banco de Dados | Alto | Correção da ordem de migração (`equipes` antes de `participantes`) e compatibilidade universal MySQL/SQLite. | Resolvido |
| **DATA-01**| Acervo Bíblico | Baixo | Parsing resiliente de 100% das 1.000 perguntas do `cadastro.dbf` clássico com mapeamento de referências e livros. | Resolvido |
| **PWA-01** | PWA / Offline | Médio | Implementação de `manifest.json`, `sw.js` com cache inteligente e registro automático nas páginas. | Resolvido |

---

## 3. Cobertura de Testes Automatizados e Navegador Real

- **Total de Testes PHPUnit:** 23
- **Total de Asserções:** 80
- **Taxa de Sucesso:** 100% (0 falhas)
- **Tempo de Execução:** ~330ms
- **Comando de Verificação:** `php artisan test`
- **Validação E2E no Navegador:** Realizada via Chrome DevTools MCP (Home, Jogo Clássico Solo com ajudas e modal, Login/Admin, Estúdio do Apresentador, Telão de Projeção, Smartphone do Participante, submissão de respostas e Pódio Final).

---

## 4. Guia Rápido de Configuração Local

```bash
# 1. Configurar variáveis de ambiente
cp .env.example .env
php artisan key:generate

# 2. Executar migrações e seeders
php artisan migrate:fresh --seed

# 3. Rodar a suíte de testes
php artisan test

# 4. Iniciar servidor local
php artisan serve
```
