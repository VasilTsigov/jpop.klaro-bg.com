<?php

namespace App\Http\Controllers;

use App\Models\ManuscriptFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\PathTraversalDetected;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    private const DISKS = ['legacy_files', 'manuscripts'];

    public function show(ManuscriptFile $file): StreamedResponse
    {
        $manuscript = $file->manuscript;
        abort_unless($manuscript, 404);
        Gate::authorize('downloadFile', [$manuscript, $file]);

        // LEGACY: ~210 file names in the dump have no file on disk (import report) -> 404
        abort_unless(in_array($file->disk, self::DISKS, true), 404);
        $disk = Storage::disk($file->disk);
        try {
            abort_unless($disk->exists($file->path), 404);
        } catch (PathTraversalDetected) {
            abort(404);
        }

        return $disk->download($file->path, $file->original_name ?: basename($file->path));
    }
}
