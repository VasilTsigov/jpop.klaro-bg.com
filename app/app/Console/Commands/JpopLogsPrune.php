<?php

namespace App\Console\Commands;

use App\Support\ActivityLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Retention of the activity journal (docs/03-plan.md §9): rows older than the per-category limit in config/jpop.php
 * are first appended to a monthly gzip CSV in storage/app/log-archive, then deleted. Legacy rows are never touched.
 */
class JpopLogsPrune extends Command
{
    protected $signature = 'jpop:logs:prune {--dry-run : only count what would be pruned}';

    protected $description = 'Archive and delete activity log rows older than their retention';

    private const COLUMNS = ['id', 'logged_at', 'category', 'level', 'user', 'role', 'ip', 'content', 'manuscript_id', 'method', 'url', 'status', 'context'];

    public function handle(): int
    {
        $dir = storage_path('app/log-archive');
        $total = 0;
        foreach ((array) config('jpop.log_retention_days') as $category => $days) {
            if ($days <= 0) {
                continue; // 0 = keep forever
            }
            $query = DB::table('activity_log')->where('category', $category)->where('logged_at', '<', now()->subDays($days));
            $count = (clone $query)->count();
            $this->line(sprintf('%-10s older than %4d days: %d', $category, $days, $count));
            if ($count === 0 || $this->option('dry-run')) {
                continue;
            }
            File::ensureDirectoryExists($dir);
            (clone $query)->orderBy('id')->chunkById(1000, function ($rows) use ($dir) {
                $this->archive($dir, $rows);
            });
            $query->delete();
            $total += $count;
        }

        if (! $this->option('dry-run')) {
            $total > 0 && ActivityLog::event('system', "Pruned $total log rows", 'info', ['rows' => $total]);
            $this->dropOldArchives($dir);
        }
        $this->info("Pruned $total rows.");

        return self::SUCCESS;
    }

    /** One gzip member per chunk appended to the file of the row's month (gunzip reads concatenated members). */
    private function archive(string $dir, $rows): void
    {
        $byMonth = [];
        foreach ($rows as $r) {
            $month = substr((string) $r->logged_at, 0, 7);
            if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
                $month = 'unknown';
            }
            $byMonth[$month][] = $r;
        }
        foreach ($byMonth as $month => $list) {
            $path = "$dir/$month.csv.gz";
            $new = ! is_file($path);
            $gz = gzopen($path, 'ab9');
            $csv = fopen('php://temp', 'w+');
            $new && fputcsv($csv, self::COLUMNS);
            foreach ($list as $r) {
                fputcsv($csv, array_map(fn ($c) => $r->$c, self::COLUMNS));
            }
            rewind($csv);
            gzwrite($gz, stream_get_contents($csv));
            fclose($csv);
            gzclose($gz);
        }
    }

    private function dropOldArchives(string $dir): void
    {
        $limit = now()->subYears((int) config('jpop.log_archive_years'))->format('Y-m');
        foreach (File::glob("$dir/*.csv.gz") as $file) {
            $month = basename($file, '.csv.gz');
            if (preg_match('/^\d{4}-\d{2}$/', $month) && $month < $limit) {
                File::delete($file);
                $this->line("Removed archive $month");
            }
        }
    }
}
