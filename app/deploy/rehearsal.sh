#!/usr/bin/env bash
# Full migration rehearsal (WSL, from app/): backup -> fresh schema -> import -> copy files -> verify -> smoke test.
# Destroys and reloads the dev DB `jpop`. Usage: deploy/rehearsal.sh [dump.sql] [legacy files/ dir]
set -euo pipefail
cd "$(dirname "$0")/.."

DUMP="${1:-../klarobg_jpop.sql}"
SRC="${2:-../files}"
TARGET="${REHEARSAL_TARGET:-$HOME/jpop-rehearsal/legacy-files}"   # stands in for the files dir on the new host
PORT="${REHEARSAL_PORT:-8099}"
step() { printf '\n== %s\n' "$*"; }
fail() { echo "FAIL: $*" >&2; exit 1; }

DBU=$(php -r 'foreach (file(".env") as $l) if (preg_match("/^DB_USERNAME=(.*)/", $l, $m)) echo trim($m[1]);')
DBP=$(php -r 'foreach (file(".env") as $l) if (preg_match("/^DB_PASSWORD=(.*)/", $l, $m)) echo trim($m[1]);')
DBN=$(php -r 'foreach (file(".env") as $l) if (preg_match("/^DB_DATABASE=(.*)/", $l, $m)) echo trim($m[1]);')

step "1. Backup of the target DB (rollback point)"
mkdir -p storage/backups
BK="storage/backups/${DBN}-$(date +%Y%m%d-%H%M%S).sql.gz"
MYSQL_PWD="$DBP" mysqldump -u"$DBU" --single-transaction "$DBN" | gzip > "$BK"
gzip -t "$BK" && echo "backup ok: $BK ($(du -h "$BK" | cut -f1))"

step "2. Fresh schema"
php artisan migrate:fresh --force

step "3. Import (dump -> staging DB -> new schema)"
php artisan jpop:import --dump="$DUMP" --files="$SRC"

step "4. Files: plan, copy only what the DB refers to, verify"
php artisan jpop:files --from="$SRC"
php artisan jpop:files --from="$SRC" --to="$TARGET" --copy
php artisan jpop:files --from="$SRC" --to="$TARGET" --verify

step "5. Counts (1:1 with the old site)"
q() { MYSQL_PWD="$DBP" mysql -u"$DBU" -N -e "$1" "$DBN"; }
[ "$(q 'select count(*) from users')" = 1164 ] || fail "users != 1164"
[ "$(q 'select count(*) from manuscripts')" = 1347 ] || fail "manuscripts != 1347"
[ "$(q 'select count(*) from activity_log')" = 7217 ] || fail "activity_log != 7217"
[ "$(q "select count(*) from manuscripts where status='rejected'")" = 1121 ] || fail "rejected != 1121"
echo "counts ok"

step "6. Smoke test as production (APP_ENV=production, files from the copied dir)"
export APP_ENV=production APP_DEBUG=false JPOP_LEGACY_FILES="$TARGET"
php artisan serve --port="$PORT" >/dev/null 2>&1 &
SRV=$!
trap 'kill $SRV 2>/dev/null || true' EXIT
for i in $(seq 1 20); do curl -s -o /dev/null "http://127.0.0.1:$PORT/users/login" && break; sleep 0.5; done
code() { curl -s -o /dev/null -w '%{http_code}' "$@"; }
chk() { [ "$2" = "$3" ] || fail "$1: got $2, expected $3"; echo "ok  $1 = $2"; }
chk "login page"                 "$(code "http://127.0.0.1:$PORT/users/login")" 200
chk "instructions (public)"      "$(code "http://127.0.0.1:$PORT/instructions")" 200
chk "contacts (public)"          "$(code "http://127.0.0.1:$PORT/contacts")" 200
chk "file without login"         "$(code "http://127.0.0.1:$PORT/files/1")" 302
chk "editor list without login"  "$(code "http://127.0.0.1:$PORT/editor/lists/inprogress")" 302
chk "abstract without login"     "$(code "http://127.0.0.1:$PORT/manuscripts/1/abstract")" 302
chk "no /storage route"          "$(code "http://127.0.0.1:$PORT/storage/anything")" 404
chk "no direct legacy URL"       "$(code "http://127.0.0.1:$PORT/files/manuscripts/JPOP1.doc")" 404
chk "consent file (public)"      "$(code "http://127.0.0.1:$PORT/instructions/consent")" 200
kill $SRV; trap - EXIT
unset APP_ENV APP_DEBUG

step "7. Queue/cron (what the hosting cron runs every minute)"
php artisan schedule:run
php artisan queue:work --stop-when-empty --max-time=20

step "8. Test suite"
php artisan test

printf '\nREHEARSAL OK\n'
