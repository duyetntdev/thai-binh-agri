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

# ── 5. Wait for MySQL using PHP PDO (reads env vars — avoids shell escaping) ──
echo ">> Waiting for MySQL at ${DB_HOST:-mysql}..."
MAX_TRIES=30
COUNT=0

# Write a small PHP script to avoid shell variable escaping issues
cat > /tmp/check_db.php << 'PHPEOF'
<?php
$host = getenv('DB_HOST') ?: 'mysql';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_DATABASE') ?: 'thai_binh_agri';
$user = getenv('DB_USERNAME') ?: 'laravel';
$pass = getenv('DB_PASSWORD') ?: 'secret';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$db}",
        $user,
        $pass,
        [PDO::ATTR_TIMEOUT => 3, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "ok";
    exit(0);
} catch (Exception $e) {
    exit(1);
}
PHPEOF

until php /tmp/check_db.php 2>/dev/null | grep -q "ok"; do
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
cat > /tmp/check_seed.php << 'PHPEOF'
<?php
$host = getenv('DB_HOST') ?: 'mysql';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_DATABASE') ?: 'thai_binh_agri';
$user = getenv('DB_USERNAME') ?: 'laravel';
$pass = getenv('DB_PASSWORD') ?: 'secret';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass);
    echo $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
} catch (Exception $e) {
    echo 0;
}
PHPEOF

USER_COUNT=$(php /tmp/check_seed.php 2>/dev/null)

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

# ── Cleanup temp files ────────────────────────────────────────────────────────
rm -f /tmp/check_db.php /tmp/check_seed.php

echo ""
echo "  App   -> http://localhost:8000"
echo "  Admin -> http://localhost:8000/admin"
echo "  PMA   -> http://localhost:8080"
echo "──────────────────────────────────────────"

exec "$@"
