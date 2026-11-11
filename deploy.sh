#!/bin/bash

set -euo pipefail

# ==============================================================================
# Script de Deploy de Produção - Bíblia Show 2.0
# Desenvolvido por Fdsmultiservices
# Suporte: (+258) 86 213 4230 | fdsmultiservices@gmail.com
# ==============================================================================

if [ -f .deploy.env ]; then
    # shellcheck disable=SC1091
    source .deploy.env
fi

SERVER="${SERVER:-ubuntu@146.235.224.99}"
KEY="${KEY:-/home/fdev-ms/.ssh/oracle-2025}"
PROJECT_DIR="${PROJECT_DIR:-/var/www/html/bibliaShow}"
ALIAS_DIR="${ALIAS_DIR:-/var/www/html/bibliashow}"
BRANCH="${BRANCH:-master}"
REPO_SSH="git@github.com:filipeive/bibliaShow.git"

echo "[INFO] Verificando estado do repositorio local..."

LOCAL_HEAD="$(git rev-parse HEAD)"
REMOTE_HEAD="$(git ls-remote origin -h "refs/heads/$BRANCH" | awk '{print $1}')"

if [[ -z "$REMOTE_HEAD" ]]; then
    echo "[ERRO] Nao foi possivel obter a branch origin/$BRANCH."
    exit 1
fi

if [[ "$LOCAL_HEAD" != "$REMOTE_HEAD" ]]; then
    echo "[AVISO] O commit local ($LOCAL_HEAD) difere de origin/$BRANCH ($REMOTE_HEAD)."
    echo "Certifique-se de executar 'git push origin $BRANCH' antes de fazer o deploy."
    exit 1
fi

echo "[OK] Commit confirmado em origin/$BRANCH ($LOCAL_HEAD)."
echo "[INFO] Iniciando deploy do Bíblia Show 2.0 para o servidor de producao ($SERVER)..."

ssh -i "$KEY" "$SERVER" "
    # Se o diretorio nao existir, clona o repositorio inicial
    if [ ! -d '$PROJECT_DIR' ]; then
        echo '[INFO] Criando diretorio de instalacao $PROJECT_DIR...' && \
        sudo mkdir -p '$PROJECT_DIR' && \
        sudo chown -R ubuntu:ubuntu '$PROJECT_DIR' && \
        git clone -b $BRANCH $REPO_SSH '$PROJECT_DIR'
    fi

    # Criar/assegurar symlink case-insensitive /var/www/html/bibliashow -> /var/www/html/bibliaShow
    if [ ! -L '$ALIAS_DIR' ]; then
        echo '[INFO] Criando symlink $ALIAS_DIR -> $PROJECT_DIR...' && \
        sudo ln -sfn '$PROJECT_DIR' '$ALIAS_DIR'
    fi

    cd '$PROJECT_DIR' && \
    echo '[INFO] Ajustando permissoes de trabalho...' && \
    sudo chown -R ubuntu:ubuntu . && \
    echo '[INFO] Sincronizando codigo-fonte (git fetch & reset)...' && \
    git fetch origin $BRANCH && \
    git reset --hard origin/$BRANCH && \
    
    # Assegurar existencia do .env em producao
    if [ ! -f .env ]; then
        echo '[INFO] Criando .env a partir do .env.example...' && \
        cp .env.example .env && \
        sed -i 's|APP_ENV=local|APP_ENV=production|g' .env && \
        sed -i 's|APP_DEBUG=true|APP_DEBUG=false|g' .env && \
        sed -i 's|APP_URL=http://localhost|APP_URL=http://146.235.224.99/bibliashow|g' .env && \
        sed -i 's|DB_CONNECTION=sqlite|DB_CONNECTION=mysql|g' .env && \
        sed -i 's|# DB_HOST=127.0.0.1|DB_HOST=127.0.0.1|g' .env && \
        sed -i 's|# DB_PORT=3306|DB_PORT=3306|g' .env && \
        sed -i 's|# DB_DATABASE=laravel|DB_DATABASE=biblia_show|g' .env && \
        sed -i 's|# DB_USERNAME=root|DB_USERNAME=fdsms|g' .env && \
        sed -i 's|# DB_PASSWORD=|DB_PASSWORD=fdsadmin|g' .env && \
        php artisan key:generate --force
    fi

    echo '[INFO] Instalando dependencias PHP otimizadas...' && \
    composer install --optimize-autoloader --no-dev --no-interaction && \
    echo '[INFO] Executando migracoes e seeders de base de dados...' && \
    php artisan migrate --force && \
    php artisan db:seed --force || true && \
    echo '[INFO] Assegurando link simbolico de storage/public...' && \
    php artisan storage:link || true && \
    echo '[INFO] Limpando caches obsoletos...' && \
    php artisan optimize:clear && \
    echo '[INFO] Regerando caches de producao (config, route, view)...' && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    echo '[INFO] Protegendo permissoes de storage e bootstrap/cache...' && \
    sudo chown -R ubuntu:www-data storage bootstrap/cache && \
    sudo chmod -R 775 storage bootstrap/cache && \
    echo '[INFO] Recarregando PHP-FPM e Nginx...' && \
    sudo systemctl reload php8.3-fpm nginx || sudo systemctl reload php8.2-fpm nginx && \
    echo '[OK] Deploy do Bíblia Show 2.0 concluido com sucesso!'
"
