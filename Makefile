# ── Nông Sản Thái Bình — Docker shortcuts ─────────────────────────────────────
.PHONY: up down build restart logs shell artisan migrate seed fresh

## Start all containers (build if needed)
up:
	sudo docker compose up -d --build

## Stop all containers
down:
	sudo docker compose down

## Rebuild PHP image only
build:
	sudo docker compose build php

## Restart all containers
restart:
	sudo docker compose restart

## Tail logs (all services)
logs:
	sudo docker compose logs -f

## Tail PHP logs only
logs-php:
	sudo docker compose logs -f php

## Open a bash shell inside the PHP container
shell:
	sudo docker compose exec php bash

## Run an artisan command  →  make artisan CMD="route:list"
artisan:
	sudo docker compose exec php php artisan $(CMD)

## Run migrations
migrate:
	sudo docker compose exec php php artisan migrate --force

## Run seeders
seed:
	sudo docker compose exec php php artisan db:seed --force

## Fresh migrate + seed (⚠ drops all tables)
fresh:
	sudo docker compose exec php php artisan migrate:fresh --seed --force

## Run composer install inside container
composer-install:
	sudo docker compose exec php composer install

## Clear all Laravel caches
cache-clear:
	sudo docker compose exec php php artisan optimize:clear

## Show container status
ps:
	sudo docker compose ps
