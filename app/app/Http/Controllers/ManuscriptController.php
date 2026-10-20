<?php

namespace App\Http\Controllers;

use App\Mail\CorrectedUploaded;
use App\Mail\SubmissionNotice;
use App\Mail\SubmissionReceived;
use App\Models\Manuscript;
use App\Support\ActivityLog;
use App\Support\EditorialMail;
use App\Support\EditorRecipients;
use App\Support\ManuscriptUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/** Author flow (docs/01-analysis.md steps A and D). Access: ManuscriptPolicy::submit / correct. */
class ManuscriptController extends Controller
{
    private const PHOTOS = 10;
    private const GRAPHS = 5;

    /** "My manuscripts" – newest first, 5 per page like the old list. */
    public function index(Request $request)
    {
        Gate::authorize('submit', Manuscript::class);

        $manuscripts = Manuscript::with('files')
            ->where('author_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(5);

        return view('manuscripts.index', compact('manuscripts'));
    }

    public function create()
    {
        Gate::authorize('submit', Manuscript::class);

        return view('manuscripts.create', ['photos' => self::PHOTOS, 'graphs' => self::GRAPHS]);
    }

    public function store(Request $request, ManuscriptUploads $uploads)
    {
        Gate::authorize('submit', Manuscript::class);

        $data = $request->validate([
            'cover_letter' => 'required|string|max:65000',
            'title' => 'required|string|max:65000',
            'abstract' => 'required|string|max:65000',
            'keywords' => 'required|string|max:65000',
            'comment' => 'nullable|string|max:65000',
            'manuscript' => ['required', ...$this->fileRules(['doc', 'docx'])],
        ] + $this->figureRules());

        $user = $request->user();
        try {
            $manuscript = DB::transaction(function () use ($data, $request, $user, $uploads) {
                // LEGACY: code = MAX(mscrCode)+1 (racy in the old site; the unique index now guards it)
                $m = Manuscript::create([
                    'id' => (int) Manuscript::max('id') + 1,
                    'code' => (int) Manuscript::max('code') + 1,
                    'author_id' => $user->id,
                    'status' => 0,
                    'title' => $data['title'],
                    'abstract' => $data['abstract'],
                    'keywords' => $data['keywords'],
                    'cover_letter' => $data['cover_letter'],
                    'comment' => $data['comment'] ?? null,
                    'submitted_at' => now(),
                ]);
                $uploads->store($m, 'manuscript', 1, $request->file('manuscript'));
                $this->storeFigures($uploads, $m, $request, 'photo', 'photo');
                $this->storeFigures($uploads, $m, $request, 'graph', 'graph');

                return $m;
            });
        } catch (\Throwable $e) {
            $uploads->rollback();
            throw $e;
        }
        $uploads->commit();

        ActivityLog::write($request, 'Submit manuscript');
        EditorialMail::deliver($user->email, new SubmissionReceived($user, $manuscript));
        EditorialMail::deliver(EditorRecipients::emails(), new SubmissionNotice($user, $manuscript));

        return redirect()->route('manuscripts.index')
            ->with('status', "Thank you! Your manuscript {$manuscript->code_label} was submitted.");
    }

    public function showCorrected(Manuscript $manuscript)
    {
        Gate::authorize('correct', $manuscript);

        return view('manuscripts.corrected', compact('manuscript'));
    }

    public function storeCorrected(Request $request, Manuscript $manuscript, ManuscriptUploads $uploads)
    {
        Gate::authorize('correct', $manuscript);
        $request->validate(['corrected' => ['required', ...$this->fileRules(['doc', 'docx', 'pdf', 'rtf', 'odt', 'zip', 'rar'])]]);

        try {
            DB::transaction(function () use ($manuscript, $request, $uploads) {
                $uploads->store($manuscript, 'corrected_manuscript', 1, $request->file('corrected'));
                $manuscript->update(['corrected_state' => 'Corrected manuscript.', 'corrected_at' => now()]);
            });
        } catch (\Throwable $e) {
            $uploads->rollback();
            throw $e;
        }
        $uploads->commit();

        ActivityLog::write($request, 'Upload Corrected File');
        EditorialMail::deliver(EditorRecipients::emails(), new CorrectedUploaded($request->user(), $manuscript));

        return redirect()->route('manuscripts.index')->with('status', 'Corrected manuscript uploaded.');
    }

    public function showFigures(Manuscript $manuscript)
    {
        Gate::authorize('correct', $manuscript);

        return view('manuscripts.figures', ['manuscript' => $manuscript, 'photos' => self::PHOTOS, 'graphs' => self::GRAPHS]);
    }

    public function storeCorrectedFigures(Request $request, Manuscript $manuscript, ManuscriptUploads $uploads)
    {
        Gate::authorize('correct', $manuscript);
        $request->validate($this->figureRules());

        try {
            DB::transaction(function () use ($manuscript, $request, $uploads) {
                // LEGACY (L3): the old page overwrote mscrGraph_N with the corrected graph; kept apart here
                $this->storeFigures($uploads, $manuscript, $request, 'photo', 'corrected_photo');
                $this->storeFigures($uploads, $manuscript, $request, 'graph', 'corrected_graph');
            });
        } catch (\Throwable $e) {
            $uploads->rollback();
            throw $e;
        }
        $uploads->commit();

        ActivityLog::write($request, 'Submit corrected figurs');

        return redirect()->route('manuscripts.index')->with('status', 'Corrected figures uploaded.');
    }

    /** photo[1..10] / graph[1..5]: the key is the slot, as in the old txtPhoto_N / txtGraphs_N fields. */
    private function figureRules(): array
    {
        $rules = [];
        foreach (['photo' => self::PHOTOS, 'graph' => self::GRAPHS] as $field => $max) {
            $rules[$field] = 'nullable|array:'.implode(',', range(1, $max));
            $rules["$field.*"] = ['nullable', ...$this->fileRules()];
        }

        return $rules;
    }

    private function fileRules(?array $extensions = null): array
    {
        return ['file', 'max:'.config('jpop.max_upload_kb'), 'extensions:'.implode(',', $extensions ?? config('jpop.upload_extensions'))];
    }

    private function storeFigures(ManuscriptUploads $uploads, Manuscript $m, Request $request, string $field, string $type): void
    {
        foreach ((array) $request->file($field) as $slot => $upload) {
            if ($upload) {
                $uploads->store($m, $type, (int) $slot, $upload);
            }
        }
    }
}

