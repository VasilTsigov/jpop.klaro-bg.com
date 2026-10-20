<?php

namespace App\Support;

use App\Models\Manuscript;
use App\Models\ManuscriptFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stores author uploads on the private `manuscripts` disk with the old naming scheme
 * (JPOP<code>, JPOP<code>_photoN, JPOP<code>_graphN, ...(corrected)) and keeps one row per type+slot.
 */
class ManuscriptUploads
{
    public const DISK = 'manuscripts';

    /** type => [directory, name suffix after "JPOP<code>"; {n} = slot] */
    private const LAYOUT = [
        'manuscript' => ['manuscripts', ''],
        'photo' => ['photographs', '_photo{n}'],
        'graph' => ['graphs', '_graph{n}'],
        'corrected_manuscript' => ['corrected', '(corrected)'],
        'corrected_photo' => ['photographs', '_photo{n}(corrected)'],
        'corrected_graph' => ['graphs', '_graph{n}(corrected)'],
        'editor_letter' => ['editletter', '_editor_letter'],
        'proof' => ['proof', '_proof'],
        'proof_letter' => ['editletter', '_proof_letter'],
    ];

    /** @var list<string> paths written during this request, removed again if the request fails */
    private array $written = [];

    /** @var list<string> replaced files, removed by commit() */
    private array $replaced = [];

    /** Replaces an earlier upload of the same type+slot (its file is removed once the new one is in place). */
    public function store(Manuscript $m, string $type, int $slot, UploadedFile $upload): ManuscriptFile
    {
        [$dir, $suffix] = self::LAYOUT[$type];
        $ext = strtolower($upload->getClientOriginalExtension());
        $name = 'JPOP'.$m->code.str_replace('{n}', (string) $slot, $suffix).'.'.$ext;
        $path = "$dir/$name";

        $disk = Storage::disk(self::DISK);
        if (! $disk->exists($path)) {
            $this->written[] = $path;
        }
        $disk->putFileAs($dir, $upload, $name);

        $old = $m->files()->where('type', $type)->where('slot', $slot)->get();
        foreach ($old as $row) {
            if ($row->disk === self::DISK && $row->path !== $path) {
                $this->replaced[] = $row->path;
            }
            $row->delete();
        }

        return $m->files()->create([
            'type' => $type, 'slot' => $slot, 'path' => $path, 'disk' => self::DISK,
            'original_name' => $name, 'uploaded_at' => now(),
        ]);
    }

    /** Call after the database transaction committed. */
    public function commit(): void
    {
        Storage::disk(self::DISK)->delete($this->replaced);
        $this->replaced = $this->written = [];
    }

    public function rollback(): void
    {
        Storage::disk(self::DISK)->delete($this->written);
        $this->replaced = $this->written = [];
    }
}
