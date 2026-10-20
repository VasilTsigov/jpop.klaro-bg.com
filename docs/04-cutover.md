# 4.9 Файлове и пускане (cutover)

## Какво се копира
- Само файловете, към които сочи БД (`manuscript_files`, диск `legacy_files`) + `attach/` (съгласие, checklist, инструкции). Остава на стария хост: файловете без връзка (на реалните данни ~3553 файла, ~1.7 GB) и 208 липсващи (отчет в `storage/app/files-manifest.csv`).
- По данни от репетицията: нужни 8391, налични 8183 (~4.7 GB), липсващи 208.
- `php artisan jpop:files --from=<files/>` – сух план; `--copy` – копира (идемпотентно, пропуска файлове със същия размер); `--verify` – сравнява размерите в целта. Целта = `JPOP_LEGACY_FILES` (извън `public_html`).
- Нови качвания: `JPOP_FILES` (private, извън `public_html`).

## Репетиция (WSL, от `app/`)
`bash deploy/rehearsal.sh [dump] [files/]` – резервно копие → `migrate:fresh` → импорт → копиране+проверка на файловете → броеве 1164/1347/7217/1121 → smoke тест като production (302 без вход, няма `/storage`, няма директен URL към файл) → cron/опашка → `php artisan test`.
Забележка: репетицията откри и поправи дефект – `jpop:import --dump` не работеше втори път (staging таблиците вече съществуват).

## Чеклист за пускане
1. Замразяване: съобщение към редакторите; нов дъмп от стария сайт и копие на `files/` (дата/час).
2. Хостинг: PHP 8.4, Composer/SSH, `.env` (production, `APP_DEBUG=false`, нов `APP_KEY`, DB, `MAIL_*` на хостинга, `QUEUE_CONNECTION=database`, `JPOP_LEGACY_FILES`, `JPOP_FILES`), document root = `app/public`.
3. `composer install --no-dev -o`, `php artisan migrate --force`, `jpop:import --dump=... --files=...`, `jpop:files --copy` + `--verify` (0 грешки), `jpop:setting editor_emails "..."`.
4. `php artisan config:cache route:cache view:cache`; права на `storage/`, `bootstrap/cache`.
5. Cron: `* * * * * php /път/artisan schedule:run >> /dev/null 2>&1`.
6. Смоук тест (както в скрипта) + ръчно: вход на стар акаунт, подаване на ръкопис, писмо стигна, сваляне на файл.
7. DNS: понижен TTL предварително → сочи към новия хост; старият сайт остава за четене (и `files/`) до края на прехода.
8. След пускане: смени DB паролата от `Connections/localhost.php`; потребители, които не влязат до дата X, получават „задай нова парола“.

## Връщане назад
Старият сайт и неговата БД остават недокоснати; връщане = DNS обратно. Преди импорт на хостинга: `mysqldump` на целевата БД (скриптът го прави в `storage/backups/`).

## Къде се въвежда нова статия
`/manuscripts/submit` („Submit Manuscripts“ в менюто) – за роли **author** и **editor** (редактор/админ също е автор); кодът е MAX+1 (`JPOP<код>`). Рецензент не подава. Редактор не може да обработва СОБСТВЕН ръкопис (рецензенти, напомняне, решение, отхвърляне, статус, писма, proof → 403) – друг редактор го прави (`ManuscriptPolicy::manageReview`).
