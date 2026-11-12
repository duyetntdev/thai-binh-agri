#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────────────────
# Chạy trên EC2 qua SSM Run Command (do .github/workflows/deploy.yml gửi lên).
# Cập nhật code tới TARGET_SHA rồi gọi scripts/deploy.sh.
#
# Biến môi trường:
#   APP_DIR      (bắt buộc) thư mục git clone của app
#   TARGET_SHA   (bắt buộc) commit cần deploy
#   APP_USER     user chạy git/composer/artisan; mặc định = chủ sở hữu APP_DIR
#   DEPLOY_RUNTIME, DOCKER_COMPOSE, PHP_FPM_SERVICE  → chuyển tiếp cho deploy.sh
# ──────────────────────────────────────────────────────────────────────────────
set -euo pipefail

: "${APP_DIR:?Thiếu APP_DIR}"
: "${TARGET_SHA:?Thiếu TARGET_SHA}"
APP_USER="${APP_USER:-$(stat -c %U "$APP_DIR")}"

# SSM chạy bằng root → chuyển sang user sở hữu code để không làm hỏng quyền file.
if [ "$(id -u)" = "0" ] && [ "$APP_USER" != "root" ]; then
    exec sudo -u "$APP_USER" -H env \
        APP_DIR="$APP_DIR" TARGET_SHA="$TARGET_SHA" APP_USER="$APP_USER" \
        DEPLOY_RUNTIME="${DEPLOY_RUNTIME:-native}" \
        DOCKER_COMPOSE="${DOCKER_COMPOSE:-docker compose}" \
        PHP_FPM_SERVICE="${PHP_FPM_SERVICE:-}" \
        bash -l "$0"
fi

cd "$APP_DIR"
echo "Chạy với user $(id -un) trong $APP_DIR"

PREV_SHA="$(git rev-parse HEAD)"
echo "Server đang ở $PREV_SHA, deploy $TARGET_SHA"
git status --short

# bootstrap/cache/*.php từng được commit nhưng bị composer ghi lại trên server.
# Tạm cất bản của server để không chặn git merge, rồi trả lại sau đó.
tmp="$(mktemp -d)"
for f in bootstrap/cache/packages.php bootstrap/cache/services.php; do
    if [ -f "$f" ]; then cp "$f" "$tmp/"; fi
    if git ls-files --error-unmatch "$f" >/dev/null 2>&1; then git checkout -- "$f"; fi
done

git fetch --prune origin master
git merge --ff-only "$TARGET_SHA"

for f in packages.php services.php; do
    if [ -f "$tmp/$f" ]; then cp "$tmp/$f" "bootstrap/cache/$f"; fi
done
rm -rf "$tmp"

PREV_SHA="$PREV_SHA" bash scripts/deploy.sh
