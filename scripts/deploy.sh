#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────────────────
# Nông Sản Thái Bình — chạy trên EC2 SAU KHI code đã được cập nhật (git merge).
# Được gọi bởi .github/workflows/deploy.yml, có thể chạy tay:
#
#   cd /path/to/app && PREV_SHA=$(git rev-parse HEAD@{1}) bash scripts/deploy.sh
#
# Biến môi trường:
#   DEPLOY_RUNTIME   native (mặc định) | docker
#   DOCKER_COMPOSE   lệnh compose khi runtime=docker (mặc định "docker compose",
#                    dùng "sudo docker compose" nếu user deploy không thuộc group docker)
#   PHP_FPM_SERVICE  tên service để reload sau deploy, ví dụ php8.2-fpm (tuỳ chọn,
#                    cần quyền "sudo -n systemctl reload <service>")
#   PREV_SHA         commit trước khi deploy — dùng để rollback code nếu lỗi
#                    xảy ra TRƯỚC bước migrate
# ──────────────────────────────────────────────────────────────────────────────
set -Eeuo pipefail

cd "$(dirname "$0")/.."

DEPLOY_RUNTIME="${DEPLOY_RUNTIME:-native}"
DOCKER_COMPOSE="${DOCKER_COMPOSE:-docker compose}"
PHP_FPM_SERVICE="${PHP_FPM_SERVICE:-}"
PREV_SHA="${PREV_SHA:-}"
NEW_SHA="$(git rev-parse HEAD)"

log() { printf '\n\033[1;32m==> %s\033[0m\n' "$*"; }
warn() { printf '\033[1;33m!! %s\033[0m\n' "$*" >&2; }

# ── Chạy lệnh trong đúng runtime ──────────────────────────────────────────────
run() {
    if [ "$DEPLOY_RUNTIME" = "docker" ]; then
        # shellcheck disable=SC2086
        $DOCKER_COMPOSE exec -T php "$@"
    else
        "$@"
    fi
}
artisan() { run php artisan "$@"; }

case "$DEPLOY_RUNTIME" in
    native|docker) ;;
    *) echo "DEPLOY_RUNTIME không hợp lệ: $DEPLOY_RUNTIME (native|docker)" >&2; exit 2 ;;
esac

if [ ! -f .env ]; then
    echo "Thiếu file .env trên server — tạo .env production trước khi deploy." >&2
    exit 1
fi

# ── Xử lý lỗi ─────────────────────────────────────────────────────────────────
STAGE="init"
MAINTENANCE=0

on_error() {
    local code=$?
    warn "Deploy lỗi ở bước: $STAGE (exit $code)"

    # Lỗi trước khi migrate: DB chưa đổi → đưa code về commit cũ cho an toàn.
    if [ -n "$PREV_SHA" ] && [ "$PREV_SHA" != "$NEW_SHA" ] \
        && { [ "$STAGE" = "composer" ] || [ "$STAGE" = "docker-build" ]; }; then
        warn "Rollback code về $PREV_SHA"
        git reset --keep "$PREV_SHA" || warn "git reset --keep thất bại, cần xử lý tay"
        run composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
            || warn "composer install khi rollback thất bại"
        artisan config:clear || true
        artisan route:clear || true
        artisan event:clear || true
    elif [ "$STAGE" = "migrate" ] || [ "$STAGE" = "cache" ]; then
        warn "Lỗi sau/khi migrate: KHÔNG tự rollback code để tránh lệch với schema DB."
        warn "Kiểm tra log, sửa và deploy lại."
    fi

    if [ "$MAINTENANCE" = "1" ]; then
        artisan up || warn "Không tắt được maintenance mode — chạy: php artisan up"
    fi
    exit "$code"
}
trap on_error ERR

log "Deploy $NEW_SHA (trước đó: ${PREV_SHA:-không rõ}) · runtime=$DEPLOY_RUNTIME"

# ── 1. Docker: chỉ rebuild khi được yêu cầu rõ ràng ───────────────────────────
# `up --build` tạo lại container php với env_file .env.docker (giá trị dev) và có
# thể ghi đè môi trường production hiện tại → mặc định KHÔNG tự rebuild.
if [ "$DEPLOY_RUNTIME" = "docker" ]; then
    STAGE="docker-build"
    if [ -n "$PREV_SHA" ] && ! git diff --quiet "$PREV_SHA" "$NEW_SHA" -- docker docker-compose.yml .env.docker; then
        if [ "${DOCKER_REBUILD:-0}" = "1" ]; then
            log "DOCKER_REBUILD=1 → build lại container"
            # shellcheck disable=SC2086
            $DOCKER_COMPOSE up -d --build --wait
        else
            warn "docker/, docker-compose.yml hoặc .env.docker thay đổi nhưng KHÔNG rebuild."
            warn "Kiểm tra env của container rồi rebuild tay, hoặc deploy lại với DOCKER_REBUILD=1."
        fi
    fi
fi

# ── 2. Maintenance mode ───────────────────────────────────────────────────────
STAGE="maintenance"
log "Bật maintenance mode"
artisan down --retry=15 --refresh=15
MAINTENANCE=1

# ── 3. Dependencies ───────────────────────────────────────────────────────────
STAGE="composer"
log "composer install"
run composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# ── 4. Database ───────────────────────────────────────────────────────────────
STAGE="migrate"
log "php artisan migrate"
artisan migrate --force --no-interaction

# ── 5. Cache ──────────────────────────────────────────────────────────────────
# Không dùng `optimize`/`view:cache`: view:cache lỗi vì module Payments chưa có
# thư mục Views. View vẫn được compile dần khi có request.
# Không dùng `optimize:clear`: nó gọi cache:clear, xoá toàn bộ cache ứng dụng.
STAGE="cache"
log "Làm mới cache"
artisan view:clear
if [ "$DEPLOY_RUNTIME" = "docker" ]; then
    # Container php nhận biến từ env_file (.env.docker, giá trị dev) nên CLI và
    # PHP-FPM có thể thấy env khác nhau. config:cache chạy từ CLI có thể "nướng"
    # giá trị dev (APP_URL, APP_KEY, APP_DEBUG) vào cache → không cache config,
    # giữ đúng hành vi của entrypoint (config:clear).
    artisan config:clear
else
    artisan config:cache
fi
artisan route:cache
artisan event:cache
artisan storage:link 2>/dev/null || true
artisan queue:restart || true

# ── 6. Reload PHP-FPM (xoá OPcache) ──────────────────────────────────────────
if [ "$DEPLOY_RUNTIME" = "native" ] && [ -n "$PHP_FPM_SERVICE" ]; then
    STAGE="reload"
    log "Reload $PHP_FPM_SERVICE"
    sudo -n systemctl reload "$PHP_FPM_SERVICE" \
        || warn "Không reload được $PHP_FPM_SERVICE (thiếu quyền sudo?)"
fi

# ── 7. Mở lại site ────────────────────────────────────────────────────────────
STAGE="up"
artisan up
MAINTENANCE=0

log "Deploy xong $NEW_SHA"
