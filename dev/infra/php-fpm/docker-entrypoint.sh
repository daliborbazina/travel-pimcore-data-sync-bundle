#!/bin/sh
set -eu

APP_DIR="/var/www/html"
BUNDLE_NAME="TravelPimcoreDataSyncBundle"
BUNDLE_CLASS="DaliborBazina\\TravelPimcoreDataSyncBundle\\TravelPimcoreDataSyncBundle"

DB_HOST="${PIMCORE_INSTALL_MYSQL_HOST_SOCKET:-pim-db}"
DB_PORT="${PIMCORE_INSTALL_MYSQL_PORT:-3306}"
DB_WAIT_SECONDS="${DB_WAIT_SECONDS:-120}"
INSTALL_BUNDLES_FLAG="${PIMCORE_INSTALL_INSTALL_BUNDLES:-false}"

log() {
  echo "[entrypoint] $*"
}

fail() {
  echo "[entrypoint] ERROR: $*" >&2
  exit 1
}

ensure_directories() {
  mkdir -p \
    "$APP_DIR/var" \
    "$APP_DIR/public/var" \
    "$APP_DIR/var/cache" \
    "$APP_DIR/var/log"
}

enable_travel_bundle() {
  log "Ensuring ${BUNDLE_NAME} is enabled in config/bundles.php..."

  APP_DIR="$APP_DIR" BUNDLE_CLASS="$BUNDLE_CLASS" php <<'PHP'
<?php
declare(strict_types=1);

$appDir = getenv('APP_DIR');
$bundleClass = getenv('BUNDLE_CLASS');
$file = $appDir . '/config/bundles.php';

if (!$appDir || !$bundleClass) {
    fwrite(STDERR, "Missing APP_DIR or BUNDLE_CLASS environment variables.\n");
    exit(1);
}

if (!file_exists($file)) {
    fwrite(STDERR, "bundles.php not found: {$file}\n");
    exit(1);
}

$content = file_get_contents($file);
if ($content === false) {
    fwrite(STDERR, "Unable to read {$file}\n");
    exit(1);
}

if (str_contains($content, $bundleClass . '::class')) {
    fwrite(STDOUT, "Bundle already enabled in bundles.php\n");
    exit(0);
}

$needle = 'return [';
if (!str_contains($content, $needle)) {
    fwrite(STDERR, "Could not find return [ in {$file}\n");
    exit(1);
}

$entry = sprintf("    %s::class => ['all' => true],\n", $bundleClass);
$updated = str_replace($needle, $needle . "\n" . $entry, $content);

if (file_put_contents($file, $updated) === false) {
    fwrite(STDERR, "Failed to update {$file}\n");
    exit(1);
}

fwrite(STDOUT, "Bundle enabled in bundles.php\n");
PHP
}

wait_for_database() {
  log "Waiting for database ${DB_HOST}:${DB_PORT}..."

  elapsed=0
  until php -r '
    $host = getenv("PIMCORE_INSTALL_MYSQL_HOST_SOCKET") ?: "pim-db";
    $port = (int) (getenv("PIMCORE_INSTALL_MYSQL_PORT") ?: 3306);
    $fp = @fsockopen($host, $port, $errno, $errstr, 2);
    if ($fp) {
        fclose($fp);
        exit(0);
    }
    fwrite(STDERR, "DB not ready: $errno $errstr\n");
    exit(1);
  '; do
    sleep 2
    elapsed=$((elapsed + 2))
    if [ "$elapsed" -ge "$DB_WAIT_SECONDS" ]; then
      fail "Database did not become ready within ${DB_WAIT_SECONDS} seconds."
    fi
  done

  log "Database is reachable."
}

ensure_bundle_class_autoloadable() {
  log "Checking bundle autoload..."

  APP_DIR="$APP_DIR" BUNDLE_CLASS="$BUNDLE_CLASS" php <<'PHP'
<?php
declare(strict_types=1);

$appDir = getenv('APP_DIR');
$bundleClass = getenv('BUNDLE_CLASS');
$autoload = $appDir . '/vendor/autoload.php';

if (!file_exists($autoload)) {
    fwrite(STDERR, "Autoload file not found: {$autoload}\n");
    exit(1);
}

require $autoload;

if (!class_exists($bundleClass)) {
    fwrite(STDERR, "Bundle class is not autoloadable: {$bundleClass}\n");
    exit(1);
}

fwrite(STDOUT, "Bundle class is autoloadable\n");
PHP
}

install_pimcore_if_needed() {
  if [ ! -f "$APP_DIR/var/config/system.yaml" ]; then
    log "Pimcore not installed yet. Running installer..."

    gosu www-data php "$APP_DIR/vendor/bin/pimcore-install" \
      --no-interaction \
      --admin-username="${PIMCORE_INSTALL_ADMIN_USERNAME}" \
      --admin-password="${PIMCORE_INSTALL_ADMIN_PASSWORD}" \
      --mysql-host-socket="${PIMCORE_INSTALL_MYSQL_HOST_SOCKET}" \
      --mysql-port="${PIMCORE_INSTALL_MYSQL_PORT}" \
      --mysql-username="${PIMCORE_INSTALL_MYSQL_USERNAME}" \
      --mysql-password="${PIMCORE_INSTALL_MYSQL_PASSWORD}" \
      --mysql-database="${PIMCORE_INSTALL_MYSQL_DATABASE}" \
      --install-bundles="${INSTALL_BUNDLES_FLAG}"

    FRESH_INSTALL=1
    log "Pimcore installation finished."
  else
    FRESH_INSTALL=0
    log "Pimcore already installed. Skipping installer."
  fi
}

verify_bundle_visible() {
  log "Checking whether ${BUNDLE_NAME} is visible to Pimcore..."

  if BUNDLE_LIST_OUTPUT="$(gosu www-data php "$APP_DIR/bin/console" pimcore:bundle:list 2>&1)"; then
    printf '%s\n' "$BUNDLE_LIST_OUTPUT"

    if printf '%s\n' "$BUNDLE_LIST_OUTPUT" | grep -q "$BUNDLE_NAME"; then
      log "${BUNDLE_NAME} is visible to Pimcore."
    else
      fail "${BUNDLE_NAME} is not visible in pimcore:bundle:list."
    fi
  else
    printf '%s\n' "$BUNDLE_LIST_OUTPUT" >&2
    fail "Failed to execute pimcore:bundle:list."
  fi
}

install_travel_bundle_if_needed() {
  if [ "${FRESH_INSTALL}" -ne 1 ]; then
    log "Skipping bundle install because this is not a fresh Pimcore installation."
    return 0
  fi

  if [ "${INSTALL_BUNDLES_FLAG}" = "true" ]; then
    log "Skipping explicit bundle install because Pimcore installer was allowed to install bundles."
    return 0
  fi

  log "Installing ${BUNDLE_NAME}..."

  gosu www-data php "$APP_DIR/bin/console" pimcore:bundle:install "$BUNDLE_NAME"

  log "${BUNDLE_NAME} installation finished."
}

fix_permissions() {
  chown -R www-data:www-data "$APP_DIR/var" "$APP_DIR/public/var" "$APP_DIR/public/bundles" 2>/dev/null || true
  chmod -R 775 "$APP_DIR/var" "$APP_DIR/public/var" "$APP_DIR/public/bundles" 2>/dev/null || true
}

run_php_as_www_data() {
  gosu www-data php "$@"
}

run_console_as_www_data() {
  gosu www-data php "$APP_DIR/bin/console" "$@"
}

main() {

  ensure_directories
  fix_permissions

  enable_travel_bundle
  wait_for_database
  ensure_bundle_class_autoloadable

  install_pimcore_if_needed
  install_travel_bundle_if_needed
  verify_bundle_visible

  fix_permissions

  exec php-fpm
}

main "$@"
