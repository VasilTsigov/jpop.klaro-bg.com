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
];
