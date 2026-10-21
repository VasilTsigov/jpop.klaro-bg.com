#!/usr/bin/env bash
# Runs on the server after a push. Source of truth is app/deploy/deploy.sh in the repo; each run refreshes the copy
# in ~/test.klaro-bg.com/deploy.sh (see docs/06-staging.md §7). Keeps .env, vendor/, storage/ and the basic-auth header of public/.htaccess.
set -euo pipefail
export PATH=/opt/cpanel/ea-php84/root/usr/bin:$PATH
ROOT="$HOME/test.klaro-bg.com"
GIT_DIR="$HOME/repos/jpop.git"
step() { printf '\n== %s\n' "$*"; }

step "1. Extract app/ from main"
git --git-dir="$GIT_DIR" archive main app | tar -x -C "$ROOT"
git --git-dir="$GIT_DIR" show main:app/deploy/deploy.sh > "$ROOT/deploy.sh.new" && chmod +x "$ROOT/deploy.sh.new" && mv "$ROOT/deploy.sh.new" "$ROOT/deploy.sh"

cd "$ROOT/app"
step "2. Keep the basic auth in front of the site"
if [ -f "$ROOT/htaccess.auth" ]; then
  cat "$ROOT/htaccess.auth" public/.htaccess > public/.htaccess.new && mv public/.htaccess.new public/.htaccess
fi

step "3. Composer"
php "$(command -v composer)" install --no-dev -o --no-interaction

step "4. Migrate and cache"
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
chmod -R u+rwX storage bootstrap/cache

step "5. Done: $(git --git-dir="$GIT_DIR" log -1 --format='%h %s' main)"
