<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class JpopSetting extends Command
{
    protected $signature = 'jpop:setting {key : e.g. editor_emails} {value? : comma separated list; omit to show, "-" to clear}';

    protected $description = 'Show or set a row of the settings table (recipients of editorial letters)';

    public function handle(): int
    {
        $key = $this->argument('key');
        $value = $this->argument('value');
        if ($value === null) {
            $this->line((string) DB::table('settings')->where('key', $key)->value('value'));

            return self::SUCCESS;
        }
        if ($key === 'editor_emails' && $value !== '-') {
            foreach (array_filter(array_map('trim', explode(',', $value))) as $address) {
                if (! filter_var($address, FILTER_VALIDATE_EMAIL)) {
                    $this->error("Invalid address: $address");

                    return self::FAILURE;
                }
            }
        }
        DB::table('settings')->upsert(
            [['key' => $key, 'value' => $value === '-' ? null : $value, 'created_at' => now(), 'updated_at' => now()]],
            ['key'], ['value', 'updated_at'],
        );
        $this->info("$key saved.");

        return self::SUCCESS;
    }
}
