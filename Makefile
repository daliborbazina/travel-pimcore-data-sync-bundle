.DEFAULT_GOAL := help

HOST_UID = $(shell id -u)
HOST_GID = $(shell id -g)
REPO_DIR = /build/repo

# =========================
# Compose
# =========================
COMPOSE_BASE = -f dev/infra/docker-compose.standalone.yaml

COMPOSE_PROJECT = travel-pimcore-data-sync-bundle

DOCKER_COMPOSE = COMPOSE_PROJECT_NAME=$(COMPOSE_PROJECT) docker compose $(COMPOSE_BASE)

# =========================
# Services
# =========================
APP_SVC = pim-php-fpm
WEB_SVC = pim-nginx
DB_SVC = pim-db

# =========================
# Console shortcuts
# =========================
CONSOLE = $(DOCKER_COMPOSE) exec --user www-data $(APP_SVC) php /var/www/html/bin/console

COMPOSER = $(DOCKER_COMPOSE) exec --user www-data $(APP_SVC) composer

# =========================
# Misc
# =========================
STANDALONE_URL = http://localhost:8080

.PHONY: help init \
	build \
	rebuild \
	up \
	down \
	destroy \
	restart \
	ps \
	logs \
	logs-app logs-web logs-db \
	shell \
	shell-web \
	db-shell \
	console \
	about \
	cache-clear \
	bundle-list \
	assets \
	lint \
	fix \
	stan \
	test \
	qa \
	sync-catalog \
	sync-catalog-dry \
	sync-availability \
	sync-availability-dry \
	preview \
	preview-pretty \
	open

help:
	@echo ""
	@echo "Travel Pimcore Data Sync Bundle - Makefile"
	@echo ""
	@echo "Environment:"
	@echo "  make init                         Prepare local environment files"
	@echo "  make build                        Build standalone images"
	@echo "  make rebuild                      Rebuild standalone images without cache"
	@echo "  make up                           Start standalone setup"
	@echo "  make down                         Stop standalone setup"
	@echo "  make destroy                      Stop standalone setup and remove volumes"
	@echo "  make restart                      Restart standalone setup"
	@echo "  make ps                           Show standalone containers"
	@echo ""
	@echo "Logs:"
	@echo "  make logs                         Follow standalone logs"
	@echo "  make logs-app                     Standalone app logs"
	@echo "  make logs-web                     Standalone web logs"
	@echo "  make logs-db                      Standalone db logs"
	@echo ""
	@echo "Shell / DB:"
	@echo "  make shell                        Open shell in standalone app container"
	@echo "  make shell-web                    Open shell in standalone web container"
	@echo "  make db-shell                     Open standalone MariaDB shell"
	@echo ""
	@echo "Application:"
	@echo "  make console                      Run Symfony console in standalone app"
	@echo "  make about                        Show standalone app info"
	@echo "  make cache-clear                  Clear standalone cache"
	@echo "  make bundle-list                  List Pimcore bundles in standalone app"
	@echo "  make assets                       Install assets in standalone app"
	@echo ""
	@echo "Runtime:"
	@echo "  make sync-catalog                 Run catalog sync in standalone app"
	@echo "  make sync-catalog-dry             Run dry-run catalog sync in standalone app"
	@echo "  make sync-availability            Run availability sync in standalone app"
	@echo "  make sync-availability-dry        Run dry-run availability sync in standalone app"
	@echo "  make preview                      Preview publish payload in standalone app"
	@echo "  make preview-pretty               Preview pretty JSON payload in standalone app"
	@echo ""
	@echo "Quality:"
	@echo "  make lint                         Run coding standard checks in standalone app"
	@echo "  make fix                          Fix coding standard issues in standalone app"
	@echo "  make stan                         Run static analysis in standalone app"
	@echo "  make test                         Run tests in standalone app"
	@echo "  make qa                           Run lint, stan and tests in standalone app"
	@echo ""
	@echo "URLs:"
	@echo "  make open                         Print standalone URL"

init:
	cp -n dev/infra/.env.example dev/infra/.env || true

# =========================
# Build
# =========================
build:
	$(DOCKER_COMPOSE) build $(APP_SVC)
	$(DOCKER_COMPOSE) build $(WEB_SVC)

rebuild:
	$(DOCKER_COMPOSE) build --no-cache $(APP_SVC)
	$(DOCKER_COMPOSE) build --no-cache $(WEB_SVC)

# =========================
# Up / Down
# =========================
up:
	$(DOCKER_COMPOSE) up -d --build $(DB_SVC) $(APP_SVC) $(WEB_SVC)

down:
	$(DOCKER_COMPOSE) down

destroy:
	$(DOCKER_COMPOSE) down -v

restart:
	$(DOCKER_COMPOSE) restart

# =========================
# Status / Logs
# =========================
ps:
	$(DOCKER_COMPOSE) ps

logs:
	$(DOCKER_COMPOSE) logs -f --tail=200

logs-app:
	$(DOCKER_COMPOSE) logs -f --tail=200 $(APP_SVC)

logs-web:
	$(DOCKER_COMPOSE) logs -f --tail=200 $(WEB_SVC)

logs-db:
	$(DOCKER_COMPOSE) logs -f --tail=200 $(DB_SVC)

# =========================
# Shell / DB
# =========================
shell:
	$(DOCKER_COMPOSE) exec --user www-data $(APP_SVC) sh

shell-web:
	$(DOCKER_COMPOSE) exec $(WEB_SVC) sh

db-shell:
	$(DOCKER_COMPOSE) exec $(DB_SVC) mariadb -upimcore -ppimcore pimcore

# =========================
# Symfony / Pimcore
# =========================
console:
	$(CONSOLE)

about:
	$(CONSOLE) about

cache-clear:
	$(CONSOLE) cache:clear --no-warmup

bundle-list:
	$(CONSOLE) pimcore:bundle:list

assets:
	$(CONSOLE) assets:install public --hard-copy --no-interaction

# =========================
# Runtime commands
# =========================
sync-catalog:
	$(CONSOLE) travel:sync:catalog

sync-catalog-dry:
	$(CONSOLE) travel:sync:catalog --dry-run

sync-availability:
	$(CONSOLE) travel:sync:availability

sync-availability-dry:
	$(CONSOLE) travel:sync:availability --dry-run

preview:
	$(CONSOLE) travel:publish:preview

preview-pretty:
	$(CONSOLE) travel:publish:preview --pretty

# =========================
# Quality
# =========================
lint:
	$(COMPOSER) lint

fix:
	$(COMPOSER) fix

stan:
	$(COMPOSER) stan

test:
	$(COMPOSER) test

qa:
	$(COMPOSER) qa
