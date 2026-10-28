<?php

namespace App\Http\Controllers;

use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class SeriesController extends Controller
{
    private const FORMATS = ['Manga', 'Manhwa', 'Novel', 'Book'];
    private const STATUSES = ['Reading', 'Completed', 'On Hold', 'Plan to Read'];

    public function index(Request $request)
    {
        $user = $request->user();
        $entries = $user->series();
        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $entries->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('author', 'like', '%'.$search.'%');
            });
        }

        if (in_array($request->query('format'), self::FORMATS, true)) {
            $entries->where('media_type', $request->query('format'));
        }

        if (in_array($request->query('status'), self::STATUSES, true)) {
            $entries->where('status', $request->query('status'));
        }

        if ($request->boolean('favorites')) {
            $entries->where('is_favorite', true);
        }

        $series = $entries->orderByDesc('is_favorite')->orderByDesc('updated_at')->get();
        $all = $user->series();
        $stats = [
            'titles' => (clone $all)->count(),
            'chapters' => (clone $all)->sum('current_chapter'),
            'completed' => (clone $all)->where('status', 'Completed')->count(),
            'rating' => (clone $all)->whereNotNull('rating')->avg('rating'),
        ];

        return view('series.index', compact('series', 'stats', 'search'));
    }

    public function create()
    {
        return view('series.form', ['series' => new Series(), 'formats' => self::FORMATS, 'statuses' => self::STATUSES]);
    }

    public function store(Request $request)
    {
        $values = $this->validatedValues($request);
        $request->user()->series()->create($values);

        return redirect()->route('library')->with('message', 'Title added to your library.');
    }

    public function show(Request $request, int $id)
    {
        $series = $request->user()->series()->findOrFail($id);
        return view('series.show', compact('series'));
    }

    public function edit(Request $request, int $id)
    {
        return view('series.form', [
            'series' => $request->user()->series()->findOrFail($id),
            'formats' => self::FORMATS,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $series = $request->user()->series()->findOrFail($id);
        $series->update($this->validatedValues($request));

        return redirect()->route('series.show', $series)->with('message', 'Your title has been updated.');
    }

    public function destroy(Request $request, int $id)
    {
        $series = $request->user()->series()->findOrFail($id);
        $series->delete();

        return redirect()->route('library')->with('message', 'Title removed from your library.');
    }

    public function increment(Request $request, int $id)
    {
        $series = $request->user()->series()->findOrFail($id);

        if ($series->status === 'Completed' || ($series->total_chapters !== null && $series->current_chapter >= $series->total_chapters)) {
            return back()->withErrors(['progress' => 'This title has reached its final chapter.']);
        }

        $series->current_chapter++;
        $series->status = $series->total_chapters !== null && $series->current_chapter >= $series->total_chapters
            ? 'Completed'
            : 'Reading';
        $series->save();

        return back()->with('message', $series->status === 'Completed' ? 'Final chapter reached. Marked as completed.' : 'Chapter progress updated.');
    }

    public function favorite(Request $request, int $id)
    {
        $series = $request->user()->series()->findOrFail($id);
        $series->is_favorite = !$series->is_favorite;
        $series->save();

        return back()->with('message', $series->is_favorite ? 'Added to favorites.' : 'Removed from favorites.');
    }

    private function validatedValues(Request $request): array
    {
        $values = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'media_type' => ['required', 'in:'.implode(',', self::FORMATS)],
            'current_chapter' => ['required', 'integer', 'min:0'],
            'total_chapters' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
            'rating' => ['nullable', 'integer', 'between:1,10'],
            'review_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $total = $values['total_chapters'] ?? null;
        $current = (int) $values['current_chapter'];
        if ($total !== null && $current > (int) $total) {
            throw ValidationException::withMessages(['current_chapter' => 'Current chapter cannot exceed the known total.']);
        }
        if ($values['status'] === 'Completed' && $total !== null && $current !== (int) $total) {
            throw ValidationException::withMessages(['status' => 'For a known total, set current chapter to the total before marking Completed.']);
        }

        return $values;
    }
}
