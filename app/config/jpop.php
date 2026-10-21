<?php

return [
    // Upload limits (KB). LEGACY: the old pages had no limit except php.ini.
    'max_upload_kb' => (int) env('JPOP_MAX_UPLOAD_KB', 51200),

    // Seen in the old data: office documents, pdf, images, archives. exe/js/php etc. are refused.
    'upload_extensions' => ['doc', 'docx', 'pdf', 'rtf', 'odt', 'xls', 'xlsx', 'ppt', 'pptx', 'csv',
        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tif', 'tiff', 'zip', 'rar'],

    // Fallback when settings.editor_emails is empty (comma separated); then all editors are used.
    // LEGACY: the addresses were hard-coded in each mail page (recipients are finalised in 4.7).
    'editor_emails' => env('JPOP_EDITOR_EMAILS', ''),

    // Activity journal retention in days (jpop:logs:prune, docs/03-plan.md §9). Rows of the legacy category are never pruned.
    'log_retention_days' => [
        'view' => (int) env('JPOP_LOG_DAYS_VIEW', 90),
        'auth' => (int) env('JPOP_LOG_DAYS_AUTH', 365),
        'security' => (int) env('JPOP_LOG_DAYS_SECURITY', 365),
        'upload' => (int) env('JPOP_LOG_DAYS_OTHER', 730),
        'editorial' => (int) env('JPOP_LOG_DAYS_OTHER', 730),
        'mail' => (int) env('JPOP_LOG_DAYS_OTHER', 730),
        'error' => (int) env('JPOP_LOG_DAYS_OTHER', 730),
        'system' => (int) env('JPOP_LOG_DAYS_OTHER', 730),
    ],
    // Monthly CSV archives of pruned rows are kept this many years.
    'log_archive_years' => (int) env('JPOP_LOG_ARCHIVE_YEARS', 5),
];
