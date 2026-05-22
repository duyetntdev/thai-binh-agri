#!/bin/bash
set -e

echo "──────────────────────────────────────────"
echo "  Nong San Thai Binh -- Container Start"
echo "──────────────────────────────────────────"

cd /var/www/html

# ── 1. Ensure storage structure exists and is writable ────────────────────────
echo ">> Setting up storage..."
mkdir -p storage/framework/views \
         storage/framework/sessions \
         storage/framework/cache/data \
         storage/logs \
         storage/app/public \
         bootstrap/cache
chmod -R 777 storage bootstrap/cache
echo "OK storage ready"

# ── 2. Copy .env if missing ────────────────────────────────────────────────────
if [ ! -f ".env" ]; then
    echo ">> Copying .env.docker -> .env"
    cp .env.docker .env
fi

# ── 3. Generate APP_KEY directly via PHP (no artisan — avoids early boot) ─────
if grep -qE "^APP_KEY=$|^APP_KEY=\"\"$" .env; then
    echo ">> Generating APP_KEY..."
    NEW_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
    sed -i "s|^APP_KEY=.*|APP_KEY=${NEW_KEY}|" .env
    echo "OK APP_KEY set"
fi

# ── 4. Install PHP dependencies ────────────────────────────────────────────────
if [ ! -f "vendor/autoload.php" ]; then
    echo ">> Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
else
    echo "OK vendor/ already exists"
fi

# ── 5. Wait for MySQL using mysqladmin ping ────────────────────────────────────
DB_HOST_VAL="${DB_HOST:-mysql}"
DB_PORT_VAL="${DB_PORT:-3306}"
DB_USER_VAL="${DB_USERNAME:-laravel}"
DB_PASS_VAL="${DB_PASSWORD:-secret}"

echo ">> Waiting for MySQL at ${DB_HOST_VAL}:${DB_PORT_VAL}..."
MAX_TRIES=60
COUNT=0

until mariadb-admin ping -h"${DB_HOST_VAL}" -P"${DB_PORT_VAL}" -u"${DB_USER_VAL}" -p"${DB_PASS_VAL}" --skip-ssl --silent 2>/dev/null; do
    COUNT=$((COUNT + 1))
    if [ "$COUNT" -ge "$MAX_TRIES" ]; then
        echo "ERROR: MySQL not ready after ${MAX_TRIES} attempts."
        exit 1
    fi
    echo "   waiting... ($COUNT/$MAX_TRIES)"
    sleep 2
done
echo "OK MySQL is ready"

# ── 6. Run migrations ─────────────────────────────────────────────────────────
echo ">> Running migrations..."
php artisan migrate --force --no-interaction

# ── 7. Seed only if users table is empty ──────────────────────────────────────
USER_COUNT=$(mariadb -h"${DB_HOST_VAL}" -P"${DB_PORT_VAL}" -u"${DB_USER_VAL}" -p"${DB_PASS_VAL}" \
    --skip-ssl "${DB_DATABASE:-thai_binh_agri}" -sNe "SELECT COUNT(*) FROM users" 2>/dev/null || echo 0)

if [ "$USER_COUNT" = "0" ]; then
    echo ">> Seeding database..."
    php artisan db:seed --force --no-interaction
else
    echo "OK Database already seeded ($USER_COUNT users)"
fi

# ── 8. Storage link ───────────────────────────────────────────────────────────
php artisan storage:link --force 2>/dev/null || true

# ── 9. Clear caches ───────────────────────────────────────────────────────────
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "  App   -> http://localhost:8000"
echo "  Admin -> http://localhost:8000/admin"
echo "  PMA   -> http://localhost:8080"
echo "──────────────────────────────────────────"

exec "$@"
