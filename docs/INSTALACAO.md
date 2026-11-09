# Instalação — Bíblia Show 2.0

## Pré-requisitos

- PHP 8.4+
- Composer 2.x
- Node.js + npm (para assets futuros)
- SQLite (incluído no PHP) ou MySQL/PostgreSQL

## Passos

```bash
# 1. Clonar o repositório
git clone <repositorio>
cd biblia-show

# 2. Instalar dependências PHP
composer install

# 3. Copiar variáveis de ambiente
cp .env.example .env
# Ajustar APP_NAME, APP_LOCALE=pt, BROADCAST_CONNECTION=reverb

# 4. Gerar chave da aplicação
php artisan key:generate

# 5. Criar banco de dados SQLite
touch database/database.sqlite

# 6. Executar migrações
php artisan migrate

# 7. Executar seeders (opcional)
php artisan db:seed

# 8. Iniciar o servidor
php artisan serve

# 9. (Opcional) Iniciar Reverb para tempo real
php artisan reverb:start
```

## Variáveis de ambiente importantes

- `APP_LOCALE=pt` — idioma principal português
- `BROADCAST_CONNECTION=reverb` — WebSockets ativos
- `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET` — credenciais Reverb
- `DB_CONNECTION=sqlite` — banco local para desenvolvimento/offline

## Testes

```bash
php artisan test
```
