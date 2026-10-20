<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;

class JpopImport extends Command
{
    protected $signature = 'jpop:import
        {--dump= : path to klarobg_jpop.sql; loaded into the legacy staging DB first}
        {--files= : path to the legacy files/ dir, to report missing files}';

    protected $description = 'Import the legacy JPOP dump into the new schema (idempotent: reloads imported tables)';

    /** legacy file column => [type, dir in legacy files/] */
    private const FILES = [
        'mscrFileName' => ['manuscript', 'manuscripts'],
        'mscrFileCorrected' => ['corrected_manuscript', 'corrected'],
        'mscrEditorsLetter' => ['editor_letter', 'editletter'],
        'mscrUploadProofFile' => ['proof', 'proof'],
        'mscrUploadProofEditorsFile' => ['proof_letter', 'editletter'],
    ];

    private const REVIEW_COLUMNS = ['mscrReviewer_', 'mscrDateToReviewer_', 'mnscConfirmReviewer_',
        'mnscrDateConfirm_', 'mscrRecommendation_', 'mscrFileReviewer_', 'mnscChecklistFileReviewer_'];

    private array $report = [];
    private ?string $filesDir = null;

    public function handle(): int
    {
        if ($dump = $this->option('dump')) {
            $this->loadDump($dump);
        }
        $this->filesDir = $this->option('files') ?: null;

        DB::transaction(function () {
            $this->wipe();
            $this->importUsers();
            $this->importManuscripts();
            $this->importLog();
            $this->importSettings();
        });

        $this->writeReport();
        foreach (['users', 'manuscripts', 'manuscript_files', 'manuscript_reviews', 'activity_log'] as $t) {
            $this->line(sprintf('%-20s %d', $t, DB::table($t)->count()));
        }
        $this->info('Report: ' . storage_path('app/import-report.csv') . ' (' . count($this->report) . ' rows)');

        return self::SUCCESS;
    }

    private function loadDump(string $path): void
    {
        $db = config('database.connections.legacy');
        $this->info("Loading $path into {$db['database']}");

        DB::connection('legacy')->statement('SELECT 1'); // fail early if the DB is missing
        // The dump has plain CREATE TABLE: start from an empty staging DB so a re-run works.
        Schema::connection('legacy')->dropAllTables();
        $result = Process::timeout(600)->input(fopen($path, 'r'))->run([
            'mysql', '-h', $db['host'], '-P', (string) $db['port'], '-u', $db['username'],
            '-p' . $db['password'], $db['database'],
        ]);
        if ($result->failed()) {
            throw new \RuntimeException('Dump load failed: ' . $result->errorOutput());
        }
    }

    private function wipe(): void
    {
        foreach (['review_invitations', 'manuscript_reviews', 'manuscript_files', 'manuscript_events',
            'manuscripts', 'users', 'activity_log', 'settings'] as $t) {
            DB::table($t)->delete();
        }
    }

    private function importUsers(): void
    {
        $roles = [1 => 'author', 2 => 'reviewer', 3 => 'editor'];
        $emails = [];
        foreach (DB::connection('legacy')->table('users')->orderBy('UserID')->cursor() as $u) {
            if ((int) $u->UserLavel === 0) {
                continue; // level 0 = never confirmed, no manuscripts (decision 2026-10-20)
            }
            $registered = $u->UserRegistrationDate;
            DB::table('users')->insert([
                'id' => $u->UserID,
                'username' => $u->UserName,
                'first_name' => $u->UserFirstName,
                'last_name' => $u->UserLastName,
                'email' => $u->UserEmail,
                'password' => null,
                'legacy_sha1' => $u->UserPassword,
                'role' => $roles[(int) $u->UserLavel],
                'email_verified_at' => $registered, // level >= 1 means the e-mail was confirmed
                'verification_code' => $u->UserVerificationCode,
                'phone' => $u->UserPhone,
                'fax' => $u->UserFax,
                'country' => $u->UserCountry,
                'state' => $u->UserState,
                'city' => $u->UserCity,
                'zip' => $u->UserZip,
                'address' => $u->UserAddress,
                'address2' => $u->UserAddress2,
                'registration_ip' => $u->UserIP,
                'registered_at' => $registered,
                'created_at' => $registered,
                'updated_at' => $registered,
            ]);
            if ($u->UserEmail !== null && $u->UserEmail !== '') {
                $emails[strtolower($u->UserEmail)][] = $u->UserID;
            }
        }
        foreach ($emails as $email => $ids) {
            if (count($ids) > 1) {
                $this->flag('duplicate_email', 'user', implode('|', $ids), $email);
            }
        }
    }

    private function importManuscripts(): void
    {
        $userIds = DB::table('users')->pluck('id')->flip();
        foreach (DB::connection('legacy')->table('mnscrpts')->orderBy('mscrID')->cursor() as $m) {
            $id = (int) $m->mscrID;
            if ($m->mscrCodeU !== 'JPOP' . $m->mscrCode) {
                $this->flag('code_mismatch', 'manuscript', $id, "code={$m->mscrCode} codeU={$m->mscrCodeU}");
            }

            $author = $userIds->has($m->mscrAutorID) ? $m->mscrAutorID : null;
            if ($author === null) {
                $this->flag('orphan_author', 'manuscript', $id, "author {$m->mscrAutorID}");
            }
            $submitted = $this->date($m->mscrUpldDate, 'manuscript', $id, 'mscrUpldDate');

            DB::table('manuscripts')->insert([
                'id' => $id,
                'legacy_id' => $id,
                'code' => $m->mscrCode,
                'author_id' => $author,
                'status' => $m->mscrStatus,
                'title' => $m->mscrFullTitle,
                'abstract' => $m->mscrAbstract,
                'keywords' => $m->mscrKeywords,
                'cover_letter' => $m->mscrCoverLeter,
                'comment' => $m->mscrComment,
                'submitted_at' => $submitted,
                'submitted_at_raw' => $m->mscrUpldDate,
                'corrected_at' => $this->date($m->mscrDateFileCorrected, 'manuscript', $id, 'mscrDateFileCorrected'),
                'corrected_state' => $this->blank($m->mscrCorrected),
                'editor_letter_file' => $this->blank($m->mscrEditorsLetter),
                'editor_letter_at' => $this->date($m->mscrDateEditorsLetter, 'manuscript', $id, 'mscrDateEditorsLetter'),
                'proof_at' => $this->date($m->mscrUploadProofDate, 'manuscript', $id, 'mscrUploadProofDate'),
                'proof_editor_at' => $this->date($m->mscrUploadProofEditorsFileDate, 'manuscript', $id, 'mscrUploadProofEditorsFileDate'),
                'created_at' => $submitted,
                'updated_at' => $submitted,
            ]);

            $this->importFiles($m, $id);
            $this->importReviews($m, $id, $userIds);
        }
    }

    private function importFiles(object $m, int $id): void
    {
        $dates = [
            'mscrFileName' => $m->mscrUpldDate,
            'mscrFileCorrected' => $m->mscrDateFileCorrected,
            'mscrEditorsLetter' => $m->mscrDateEditorsLetter,
            'mscrUploadProofFile' => $m->mscrUploadProofDate,
            'mscrUploadProofEditorsFile' => $m->mscrUploadProofEditorsFileDate,
        ];
        foreach (self::FILES as $col => [$type, $dir]) {
            $this->addFile($id, $type, 1, $dir, $m->$col, $dates[$col]);
        }
        for ($i = 1; $i <= 10; $i++) {
            $this->addFile($id, 'photo', $i, 'photographs', $m->{"mscrFotos_$i"}, $m->mscrUpldDate);
            $this->addFile($id, 'corrected_photo', $i, 'photographs', $m->{"mscrFotoCorr_$i"}, $m->mscrDateFileCorrected);
        }
        for ($i = 1; $i <= 5; $i++) {
            $this->addFile($id, 'graph', $i, 'graphs', $m->{"mscrGraph_$i"}, $m->mscrUpldDate);
            $this->addFile($id, 'corrected_graph', $i, 'graphs', $m->{"mscrGraphCorr_$i"}, $m->mscrDateFileCorrected);
        }
    }

    private function addFile(int $mid, string $type, int $slot, string $dir, ?string $name, ?string $date): ?int
    {
        $name = $this->blank($name);
        if ($name === null) {
            return null;
        }
        if ($this->filesDir && !is_file("{$this->filesDir}/$dir/$name")) {
            $this->flag('missing_file', 'manuscript', $mid, "$type $dir/$name");
        }

        return DB::table('manuscript_files')->insertGetId([
            'manuscript_id' => $mid, 'type' => $type, 'slot' => $slot, 'path' => "$dir/$name",
            'original_name' => $name, 'uploaded_at' => $this->date($date, 'file', $mid, $type),
        ]);
    }

    private function importReviews(object $m, int $id, $userIds): void
    {
        for ($n = 1; $n <= 3; $n++) {
            $has = false;
            foreach (self::REVIEW_COLUMNS as $c) {
                $has = $has || $this->blank($m->{$c . $n}) !== null;
            }
            if (!$has) {
                continue;
            }

            $rid = $this->blank($m->{"mscrReviewer_$n"});
            if ($rid !== null && !$userIds->has((int) $rid)) {
                $this->flag('dangling_reviewer', 'manuscript', $id, "slot $n reviewer $rid");
                $rid = null;
            }
            $name = $this->blank($m->{"mscrReviewerFullName_$n"});
            if ($name === "Reviewer $n") {
                $name = null; // LEGACY: placeholder written when a slot is reset
            }

            $confirm = strtolower(trim((string) $m->{"mnscConfirmReviewer_$n"}));
            $state = match (true) {
                str_starts_with($confirm, 'conf'), str_starts_with($confirm, 'com') => 'confirmed', // LEGACY: " Comfirmed" typo
                str_starts_with($confirm, 'not') => 'declined',
                default => 'pending',
            };
            $rec = $this->blank($m->{"mscrRecommendation_$n"});

            $reviewFile = $this->addFile($id, 'review', $n, 'reviewered', $m->{"mscrFileReviewer_$n"}, $m->{"mscrDateFileReviewer_$n"});
            $checklist = $this->addFile($id, 'checklist', $n, 'checklist', $m->{"mnscChecklistFileReviewer_$n"}, $m->{"mscrDateFileReviewer_$n"});

            DB::table('manuscript_reviews')->insert([
                'manuscript_id' => $id,
                'slot' => $n,
                'reviewer_id' => $rid,
                'reviewer_name' => $name,
                'invited_at' => $this->date($m->{"mscrDateToReviewer_$n"}, 'review', $id, "invited_$n"),
                'confirmed_state' => $state,
                'confirmed_at' => $this->date($m->{"mnscrDateConfirm_$n"}, 'review', $id, "confirmed_$n"),
                'recommendation' => in_array($rec, ['Rejected', 'Major revision', 'Minor revision'], true) ? $rec : null,
                'review_file_id' => $reviewFile,
                'checklist_file_id' => $checklist,
                'reviewed_at' => $this->date($m->{"mscrDateFileReviewer_$n"}, 'review', $id, "reviewed_$n"),
            ]);
        }
    }

    private function importLog(): void
    {
        foreach (DB::connection('legacy')->table('log')->orderBy('logID')->cursor() as $l) {
            DB::table('activity_log')->insert([
                'id' => $l->logID, 'user' => $l->logUser, 'ip' => $l->logIP,
                'content' => $l->logContent, 'logged_at' => $l->logDate,
            ]);
        }
    }

    private function importSettings(): void
    {
        $h = DB::connection('legacy')->table('helpfiles')->first();
        if ($h) {
            foreach (['help.checklist' => $h->filesChecklist, 'help.instructions' => $h->filesInstructions,
                'help.consent_for_authors' => $h->filesToAutors] as $k => $v) {
                DB::table('settings')->insert(['key' => $k, 'value' => $v]);
            }
        }
    }

    private function blank(?string $v): ?string
    {
        $v = $v === null ? null : trim($v);

        return $v === '' ? null : $v;
    }

    /** Legacy dates are dd.mm.yyyy, sometimes padded or prefixed ("to reviewer: "). */
    private function date(?string $raw, string $entity, int $id, string $field): ?string
    {
        $v = $this->blank($raw);
        if ($v === null) {
            return null;
        }
        if (preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})/', $v, $p) && checkdate((int) $p[2], (int) $p[1], (int) $p[3])) {
            return Carbon::create((int) $p[3], (int) $p[2], (int) $p[1])->toDateTimeString();
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $v)) {
            return $v;
        }
        $this->flag('bad_date', $entity, $id, "$field='$raw'");

        return null;
    }

    private function flag(string $issue, string $entity, int|string $id, string $detail): void
    {
        $this->report[] = [$issue, $entity, $id, $detail];
    }

    private function writeReport(): void
    {
        $f = fopen(storage_path('app/import-report.csv'), 'w');
        fputcsv($f, ['issue', 'entity', 'id', 'detail']);
        foreach ($this->report as $r) {
            fputcsv($f, $r);
        }
        fclose($f);
    }
}
