<?php

namespace App\Http\Controllers;

use App\Models\Manuscript;
use App\Support\ActivityLog;
use Illuminate\Http\Request;

/** Editors' search over authors and manuscript texts (old search/main.php + search_result.php). */
class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $manuscripts = null;

        if ($request->has('q')) {
            ActivityLog::write($request, 'Enter main search site');
            // LEGACY: LIKE %text% over author name and title/abstract/cover letter/keywords, INNER JOIN to the author.
            // Now paginated, and LIKE wildcards in the input are literal.
            $like = '%'.preg_replace('/[!%_]/', '!$0', $q).'%';
            $match = fn ($query, string $column) => $query->orWhereRaw("$column LIKE ? ESCAPE '!'", [$like]);
            $manuscripts = Manuscript::with(['author', 'reviews'])
                ->has('author')
                ->where(function ($w) use ($match) {
                    foreach (['abstract', 'title', 'cover_letter', 'keywords'] as $column) {
                        $match($w, $column);
                    }
                    $w->orWhereHas('author', fn ($a) => $a->where(function ($n) use ($match) {
                        $match($n, "CONCAT(first_name, ' ', last_name)");
                        $match($n, 'first_name');
                        $match($n, 'last_name');
                    }));
                })
                ->orderByDesc('id')
                ->paginate(15)->withQueryString();
        }

        return view('pages.search', compact('q', 'manuscripts'));
    }
}
