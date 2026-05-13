#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
ENV_FILE="${ENV_FILE:-${APP_DIR}/.env}"
BACKUP_DIR="${DB_BACKUP_DIR:-${APP_DIR}/storage/app/backups/database}"
KEEP_DAYS="${DB_BACKUP_KEEP_DAYS:-30}"

env_value() {
  local key="$1"
  php -r '
    $file = $argv[1];
    $key = $argv[2];
    $value = "";
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === "" || str_starts_with($line, "#") || !str_contains($line, "=")) {
            continue;
        }
        [$name, $rawValue] = explode("=", $line, 2);
        if (trim($name) === $key) {
            $value = trim($rawValue);
            $value = trim($value, "\"'\''");
            break;
        }
    }
    echo $value;
  ' "$ENV_FILE" "$key"
}

if [[ ! -f "$ENV_FILE" ]]; then
  echo "Missing .env file: $ENV_FILE" >&2
  exit 1
fi

DB_HOST="$(env_value DB_HOST)"
DB_PORT="$(env_value DB_PORT)"
DB_DATABASE="$(env_value DB_DATABASE)"
DB_USERNAME="$(env_value DB_USERNAME)"
DB_PASSWORD="$(env_value DB_PASSWORD)"

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"

if [[ -z "$DB_DATABASE" || -z "$DB_USERNAME" ]]; then
  echo "DB_DATABASE and DB_USERNAME must be configured in .env" >&2
  exit 1
fi

mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR" || true

STAMP="$(date +%Y%m%d_%H%M%S)"
SAFE_DB_NAME="$(printf '%s' "$DB_DATABASE" | tr -c 'A-Za-z0-9_.-' '_')"
OUT_SQL="${BACKUP_DIR}/${SAFE_DB_NAME}_${STAMP}.sql"
OUT_GZ="${OUT_SQL}.gz"

cleanup() {
  rm -f "$OUT_SQL"
}
trap cleanup EXIT

dump_database() {
  local extra_args=("$@")
  MYSQL_PWD="$DB_PASSWORD" mysqldump \
    --single-transaction \
    --quick \
    --routines \
    --triggers \
    --events \
    "${extra_args[@]}" \
    -h "$DB_HOST" \
    -P "$DB_PORT" \
    -u "$DB_USERNAME" \
    "$DB_DATABASE" > "$OUT_SQL"
}

if ! dump_database --set-gtid-purged=OFF 2>"${OUT_SQL}.err"; then
  if grep -qi 'set-gtid-purged' "${OUT_SQL}.err"; then
    rm -f "$OUT_SQL"
    dump_database
  else
    cat "${OUT_SQL}.err" >&2
    exit 1
  fi
fi
rm -f "${OUT_SQL}.err"

gzip -9 "$OUT_SQL"
trap - EXIT

ln -sfn "$(basename "$OUT_GZ")" "${BACKUP_DIR}/latest.sql.gz" 2>/dev/null || true
find "$BACKUP_DIR" -type f -name "${SAFE_DB_NAME}_*.sql.gz" -mtime +"$KEEP_DAYS" -delete

echo "$OUT_GZ"
