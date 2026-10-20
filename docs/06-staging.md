# 6. Тестов сървър (superhosting.bg, `test.klaro-bg.com`)

Цел: жив тест върху реални данни на хостинга, без да се пипа старият сайт. Това е и репетиция на пускането (виж `04-cutover.md`) – същите стъпки, друга БД и друг домейн.

## Решения
- Данните са реални (без анонимизация, по решение от 2026-10-20) → защитата е на ниво сървър: basic auth, `noindex`, **всички писма към един адрес** (`MAIL_REDIRECT_TO`).
- Отделна БД и отделна папка за файловете; старият сайт и БД не се докосват.
- Разработката остава локално; на сървъра само се качва и проверява.

## 0. Преди да започнем (проверка на плана на хостинга)
- [ ] PHP 8.4 за поддомейна (cPanel → Select PHP Version); разширения: `pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, fileinfo, bcmath`.
- [ ] SSH + Composer (иначе `vendor/` се качва готов: `composer install --no-dev -o` локално/WSL).
- [ ] Свободно място: нужни ~4.7 GB файлове (само тези, към които сочи БД) + БД. Ако не достига – копират се само последните N години или тестът е без файлове (`jpop:files` без `--copy`).
- [ ] Нова MySQL база + потребител за тестовия поддомейн + втора база за staging (`LEGACY_DB_DATABASE`, connection `legacy`, същият потребител с права и на двете; нужни са 2 бази в плана). Connection `legacy` е с драйвер `mariadb` – ако хостингът е чист MySQL, да се провери импортът (при проблем: драйвер `mysql`).
- [ ] Може ли document root на поддомейна да сочи към `app/public`.
- [ ] Cron на всяка минута.

## 1. Подготовка на сървъра
```
~/test.klaro-bg.com/            # извън public_html
  app/                          # Laravel (git clone или upload)
  legacy-files/                 # JPOP_LEGACY_FILES (read-only файлове от стария сайт)
  files/                        # JPOP_FILES (нови качвания, private)
public_html/test -> app/public  # или document root на поддомейна = app/public
```
## 2. `.env` на тестовия сървър
```
APP_NAME=JPOP-test
APP_ENV=staging
APP_DEBUG=false
APP_KEY=                 # php artisan key:generate
APP_URL=https://test.klaro-bg.com
APP_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
LEGACY_DB_DATABASE=...   # втората база (staging копие на стария дъмп)

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp         # SMTP на хостинга
MAIL_HOST=...
MAIL_PORT=...
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=test@klaro-bg.com
MAIL_REDIRECT_TO=v.tsigov@gmail.com   # ЗАДЪЛЖИТЕЛНО: всички писма идват тук, не до реални хора

JPOP_LEGACY_FILES=/home/<user>/test.klaro-bg.com/legacy-files
JPOP_FILES=/home/<user>/test.klaro-bg.com/files
```
`MAIL_REDIRECT_TO` е реализирано в `AppServiceProvider` (`Mail::alwaysTo`) и важи за всички писма и известия. Преди първия импорт: `php artisan tinker --execute="Mail::raw('x', fn(\$m)=>\$m->to('someone@example.com')->subject('t'));"` → трябва да пристигне при теб, не при `someone@example.com`.

## 3. Защита на достъпа
`public/.htaccess` (само на теста) – basic auth:
```
AuthType Basic
AuthName "JPOP test"
AuthUserFile /home/<user>/test.klaro-bg.com/.htpasswd
Require valid-user
```
+ `X-Robots-Tag: noindex, nofollow` (`Header set X-Robots-Tag "noindex, nofollow"`). Паролата се създава с `htpasswd -c`.

## 4. Качване и пускане
1. Локално (WSL): `composer install --no-dev -o`; качване на `app/` без `.env`, `storage/logs`, `node_modules` (git pull, ако има SSH).
2. Качване на дъмпа (`klarobg_jpop.sql`, 6 MB) и на `files/` → `legacy-files/` (rsync/SFTP; ~4.7 GB, виж `jpop:files` за точния списък).
3. На сървъра, от `app/`:
```
php artisan key:generate
php artisan migrate --force
php artisan jpop:import --dump=../klarobg_jpop.sql --files=../legacy-files
php artisan jpop:files --from=../legacy-files            # сух план
php artisan jpop:files --from=../legacy-files --verify   # 0 грешки
php artisan jpop:setting editor_emails "<тестови адреси>"
php artisan config:cache && php artisan route:cache && php artisan view:cache
chmod -R u+rwX storage bootstrap/cache
```
4. Cron: `* * * * * /usr/local/bin/php /home/<user>/test.klaro-bg.com/app/artisan schedule:run >> /dev/null 2>&1`
5. Смоук тест (отвън): `/users/login` = 200, `/files/1` без вход = 302, `/storage/x` = 404, `/files/manuscripts/JPOP1.doc` = 404 (както в `deploy/rehearsal.sh`, стъпка 6).
6. Броеве: users 1164, manuscripts 1347, activity_log 7217, status=1 → 1121.

## 5. Ръчни тестове (чеклист)
- [ ] Вход със стар акаунт (SHA1 → bcrypt), забравена парола (писмото идва на `MAIL_REDIRECT_TO`).
- [ ] Автор: подаване на ръкопис, качване на файл, сваляне.
- [ ] Редактор: рецензенти, напомняне, решение, proof.
- [ ] Рецензент: вижда само свои; линк за съгласие (подписан, 30 дни, „преизпрати").
- [ ] Cron/опашка: писмата излизат в рамките на минута.
- [ ] Сравнение със стария сайт – `05-manual-comparison.md`.

## 6. Повторение и почистване
- Ново чисто пускане: `php artisan migrate:fresh --force` + стъпки 3.3.
- Резервно копие преди импорт: `mysqldump` (виж `deploy/rehearsal.sh`, стъпка 1).
- След теста: изтриване на поддомейна, БД и `legacy-files/` (реални лични данни!).

## Риск/бележки
- Реални данни на чужд (shared) хостинг → basic auth е задължителен, а копията се изтриват след теста.
- `Mail::alwaysTo` пренасочва и CC/BCC; оригиналният получател се губи – при нужда от проверка кой е получател се гледа `storage/logs`/activity_log.
