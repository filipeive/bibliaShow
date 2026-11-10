# Instalação — Bíblia Show 2.0 (Edição Clássica & Ao Vivo)

## Pré-requisitos

- PHP 8.2+ / 8.4+
- Composer 2.x
- SQLite (incluído no PHP) ou MySQL/PostgreSQL

## Passos de Instalação

```bash
# 1. Clonar o repositório oficial
git clone https://github.com/filipeive/bibliaShow.git
cd bibliaShow

# 2. Instalar dependências PHP
composer install

# 3. Copiar variáveis de ambiente
cp .env.example .env

# 4. Gerar chave da aplicação
php artisan key:generate

# 5. Criar banco de dados SQLite (se ainda não existir)
touch database/database.sqlite

# 6. Executar migrações e importar as 660 perguntas clássicas (cadastro.dbf)
php artisan migrate --seed

# 7. Iniciar o servidor local
php artisan serve
```

## Importação Direta da Base Clássica (`cadastro.dbf`)

O arquivo original FoxPro `database/data/cadastro.dbf` já está versionado no projeto. Para reimportar ou sincronizar manualmente as 660 perguntas originais com suas dicas bíblicas e convidados:

```bash
php artisan db:seed --class=QuestoesClassicoSeeder
```

Você também pode sincronizar com 1 clique através do **Painel de Administração (`/admin`)**.

## Credenciais de Demonstração (`/login`)

- **E-mail**: `admin@igreja.com`
- **Senha**: `password`

## Variáveis de Ambiente Importantes

- `APP_LOCALE=pt` — idioma principal português
- `DB_CONNECTION=sqlite` — banco local pronto para uso imediato e offline
- `BROADCAST_CONNECTION=reverb` — WebSockets opcionais (o sistema já conta com sincronização HTTP resiliente a cada 2,5s)
