<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class JpopFiles extends Command
{
    protected $signature = 'jpop:files
        {--from= : legacy files/ dir (source)}
        {--to= : target dir (default: the legacy_files disk root, JPOP_LEGACY_FILES)}
        {--copy : copy the files the database refers to (without it: dry run, report only)}
        {--verify : compare sizes in the target}';

    protected $description = 'Plan/copy/verify the legacy files the new site needs (referenced in manuscript_files + attach/); the rest is only reported';

    public function handle(): int
    {
        $from = rtrim((string) $this->option('from'), '/\\');
        $to = rtrim((string) ($this->option('to') ?: config('filesystems.disks.legacy_files.root')), '/\\');
        if (!$from || !is_dir($from)) {
            $this->error('--from must be an existing directory');
            return self::FAILURE;
        }
        if (is_dir($to) && realpath($from) === realpath($to)) {
            $this->error('source and target are the same');
            return self::FAILURE;
        }

        $needed = DB::table('manuscript_files')->where('disk', 'legacy_files')->pluck('path')->unique()->all();
        foreach (glob("$from/attach/*") ?: [] as $f) {
            $needed[] = 'attach/' . basename($f); // CONSENT/Checklist/Instructions (helpfiles)
        }
        $needed = array_values(array_unique($needed));

        $present = $missing = $copied = $bad = 0;
        $bytes = 0;
        $manifest = [];
        foreach ($needed as $rel) {
            $src = "$from/$rel";
            if (str_contains($rel, '..') || !is_file($src)) {
                $missing++;
                $manifest[] = [$rel, '', 'missing'];
                continue;
            }
            $size = filesize($src);
            $present++;
            $bytes += $size;
            $state = 'planned';
            $dst = "$to/$rel";
            if ($this->option('copy')) {
                if (!is_file($dst) || filesize($dst) !== $size) {
                    @mkdir(dirname($dst), 0755, true);
                    $state = copy($src, $dst) ? 'copied' : 'failed';
                    $state === 'copied' ? $copied++ : $bad++;
                } else {
                    $state = 'same';
                }
            }
            if ($this->option('verify')) {
                if (!is_file($dst)) {
                    $state = 'not_in_target';
                    $bad++;
                } elseif (filesize($dst) !== $size) {
                    $state = 'size_mismatch';
                    $bad++;
                } else {
                    $state = 'ok';
                }
            }
            $manifest[] = [$rel, $size, $state];
        }

        // Everything else in the source is only reported (not copied).
        $known = array_flip($needed);
        $unusedN = $unusedBytes = 0;
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($from, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            $rel = str_replace('\\', '/', substr($file->getPathname(), strlen($from) + 1));
            if (!isset($known[$rel])) {
                $unusedN++;
                $unusedBytes += $file->getSize();
            }
        }

        $path = storage_path('app/files-manifest.csv');
        @mkdir(dirname($path), 0755, true);
        $h = fopen($path, 'w');
        fputcsv($h, ['path', 'size', 'state'], ',', '"', '');
        foreach ($manifest as $row) {
            fputcsv($h, $row, ',', '"', '');
        }
        fclose($h);

        $mb = fn ($b) => round($b / 1048576, 1) . ' MB';
        $this->line(sprintf('needed %d: present %d (%s), missing %d', count($needed), $present, $mb($bytes), $missing));
        $this->line(sprintf('not referenced (NOT copied): %d files, %s', $unusedN, $mb($unusedBytes)));
        if ($this->option('copy')) {
            $this->line("copied $copied, failed $bad");
        } elseif (!$this->option('verify')) {
            $this->comment('dry run; use --copy to copy');
        }
        $this->line("Manifest: $path");

        return $bad ? self::FAILURE : self::SUCCESS;
    }
}
