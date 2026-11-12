# Auto deploy lên EC2

Mỗi lần push lên `master`, GitHub Actions ([.github/workflows/deploy.yml](../.github/workflows/deploy.yml)) sẽ:

1. Chạy `phpunit --testsuite Unit`. Test lỗi thì dừng, không deploy.
2. Lấy credentials AWS tạm thời bằng **OIDC**: assume role `thai-binh-agri-github-deploy`. Không có access key hay SSH key nào lưu trên GitHub.
3. Gửi [scripts/pull-and-deploy.sh](../scripts/pull-and-deploy.sh) tới EC2 `basion` qua **SSM Run Command**. Script chuyển sang user sở hữu thư mục app, `git fetch` + `git merge --ff-only <commit vừa push>`, rồi chạy [scripts/deploy.sh](../scripts/deploy.sh):
   `artisan down` → `composer install --no-dev` → `artisan migrate --force` → `config:cache`, `route:cache`, `event:cache`, `view:clear` → `queue:restart` → reload PHP-FPM (tuỳ chọn) → `artisan up`.
4. In log ra GitHub Actions. Log đầy đủ nằm ở CloudWatch Logs `/thai-binh-agri/deploy` (giữ 30 ngày).
5. Gọi `GET <APP_URL>/up`, phải trả 200.

Hai lần deploy không bao giờ chạy cùng lúc. Có thể chạy tay ở tab **Actions → Deploy production → Run workflow** (chọn nhánh `master`).

```
GitHub Actions ──OIDC token──▶ STS ──▶ role thai-binh-agri-github-deploy
      │                                        │ ssm:SendCommand (chỉ basion, chỉ AWS-RunShellScript)
      ▼                                        ▼
  CloudWatch Logs ◀── SSM Agent trên EC2 basion (role thai-binh-agri-ec2)
                               │ git pull, composer, artisan (chạy bằng user sở hữu APP_DIR)
                               ▼
                       https://duyetnt.com
```

## Tài nguyên AWS (đã tạo)

| Tài nguyên | Tên | Quyền |
|---|---|---|
| OIDC provider | `token.actions.githubusercontent.com` | audience `sts.amazonaws.com` |
| IAM role cho GitHub | `thai-binh-agri-github-deploy` | Trust: chỉ `repo:duyetntdev/thai-binh-agri:ref:refs/heads/master`. Policy `ssm-deploy-basion`: `ssm:SendCommand` lên đúng instance `i-0f71e491fff656a64` với document `AWS-RunShellScript`; đọc trạng thái lệnh; đọc log group deploy |
| IAM role cho EC2 | `thai-binh-agri-ec2` (instance profile cùng tên) | `AmazonSSMManagedInstanceCore` + ghi log vào `/thai-binh-agri/deploy` |
| Log group | `/thai-binh-agri/deploy` | retention 30 ngày |

Branch khác, pull request hay fork **không** assume được role (sai `sub`). Nếu đổi tên repo/branch, phải sửa trust policy của role.

## Cấu hình trên GitHub

Không cần secret nào. Các giá trị mặc định đã nằm trong workflow. Chỉ cần ghi đè bằng **Settings → Secrets and variables → Actions → Variables** khi thay đổi:

| Variable | Mặc định |
|---|---|
| `AWS_ROLE_ARN` | `arn:aws:iam::886831123590:role/thai-binh-agri-github-deploy` |
| `EC2_INSTANCE_ID` | `i-0f71e491fff656a64` |
| `APP_DIR` | `/var/www/thai-binh-agri` |
| `DEPLOY_RUNTIME` | `docker` (production hiện chạy docker compose), hoặc `native` |
| `PHP_FPM_SERVICE` | để trống = không reload |
| `DOCKER_COMPOSE` | `docker compose` |
| `APP_URL` | `https://duyetnt.com` |

## Hiện trạng production (kiểm tra qua SSM ngày 12/11/2026)

- `/var/www/thai-binh-agri`, chủ sở hữu `root`, nhánh `master`, remote HTTPS. SSM chạy bằng root nên deploy cũng chạy bằng root.
- nginx trên host (TLS, certbot timer) → `proxy_pass http://127.0.0.1:8000` → các container `agri_nginx`, `agri_php`, `agri_mysql`, `agri_redis`, `agri_phpmyadmin` của `docker-compose.yml`.
- Host không cài `php`/`composer`; mọi lệnh chạy qua `docker compose exec -T php …`.
- Container `agri_php` đang có môi trường production (`APP_URL=https://duyetnt.com`), khác với `.env.docker` dev trong repo. Cách môi trường này được đặt chưa rõ; tạo lại container có thể làm mất nó (xem bên dưới).

Yêu cầu chung:

- Phải có sẵn file `.env` production.
- Runtime `native` (nếu sau này chuyển): cần `php` 8.2 và `composer` trong `PATH` của user sở hữu thư mục app; đặt `PHP_FPM_SERVICE` nếu cần reload PHP-FPM.

## Khi deploy lỗi

| Lỗi ở bước | Script làm gì |
|---|---|
| `git merge --ff-only` (server có commit/sửa đổi lệch với `master`) | Dừng trước khi đụng vào gì. Xem `git status` trên server. |
| `composer install` | Đưa code về commit cũ (`git reset --keep`), cài lại dependency, mở lại site. |
| `migrate` hoặc cache | **Không** rollback code (schema có thể đã đổi). Mở lại site, workflow báo đỏ. Sửa và push lại. |

Script luôn tắt maintenance mode trước khi thoát, kể cả khi lỗi.

## Vào server không cần SSH

Khi EC2 đã đăng ký SSM, có thể mở shell bằng Session Manager (cần quyền admin AWS và [session-manager-plugin](https://docs.aws.amazon.com/systems-manager/latest/userguide/session-manager-working-with-install-plugin.html)):

```bash
aws ssm start-session --target i-0f71e491fff656a64 --region ap-southeast-1 --profile duyetnt
```

Khi đã quen cách này, có thể đóng cổng 22 trong security group `launch-wizard-4`.

## Runtime `docker`

Khi `DEPLOY_RUNTIME=docker`:

- Mọi lệnh `php`/`composer` chạy qua `docker compose exec -T php …` (container đang chạy, không tạo lại).
- **Không** chạy `config:cache`, chỉ `config:clear`, giống entrypoint. Route và event vẫn được cache.
- **Không tự rebuild.** `docker compose up --build` tạo lại container `php` với `env_file: .env.docker` (giá trị dev: `APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://localhost:8000`, `APP_KEY` dev). Việc đó sẽ ghi đè môi trường production đang chạy. Khi commit đổi `docker/`, `docker-compose.yml` hoặc `.env.docker`, deploy chỉ in cảnh báo. Rebuild tay sau khi đã có file env production cho compose, hoặc chạy lại với `DOCKER_REBUILD=1`.
- `docker-compose.yml` là cấu hình dev: mật khẩu MySQL cố định trong repo, phpMyAdmin, MySQL/Redis/phpMyAdmin bind `0.0.0.0` (hiện chỉ được security group chặn). Nên tách một file compose riêng cho production.

## Ghi chú

- `view:cache` đang bị bỏ qua vì module Payments thiếu thư mục `Views/`. Khi thêm thư mục đó, có thể chuyển sang `php artisan optimize`.
