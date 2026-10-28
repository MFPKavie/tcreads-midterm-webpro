@extends('layout')
@section('title', 'Your library')
@section('content')
<div class="heading">
    <div><p class="eyebrow">YOUR READING SHELF</p><h1>Your library</h1><p>Every chapter, in one place.</p></div>
    <a class="button primary" href="{{ route('series.create') }}">+ Add title</a>
</div>
<section class="stats">
    <div><small>Total titles</small><strong>{{ $stats['titles'] }}</strong></div>
    <div><small>Chapters read</small><strong>{{ $stats['chapters'] }}</strong></div>
    <div><small>Completed</small><strong>{{ $stats['completed'] }}</strong></div>
    <div><small>Average rating</small><strong>{{ $stats['rating'] === null ? '-' : number_format($stats['rating'], 1).'/10' }}</strong></div>
</section>
<form method="get" action="{{ route('library') }}" class="filters">
    <input name="q" value="{{ $search }}" placeholder="Search title or author" aria-label="Search title or author">
    <select name="format" aria-label="Format"><option value="">All formats</option>
        @foreach (['Manga', 'Manhwa', 'Novel', 'Book'] as $format)<option value="{{ $format }}" @selected(request('format') === $format)>{{ $format }}</option>@endforeach
    </select>
    <select name="status" aria-label="Status"><option value="">All statuses</option>
        @foreach (['Reading', 'Completed', 'On Hold', 'Plan to Read'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach
    </select>
    <label><input type="checkbox" name="favorites" value="1" @checked(request()->boolean('favorites'))> Favorites only</label>
    <button>Filter</button>
    @if (request()->query())<a href="{{ route('library') }}">Clear</a>@endif
</form>
<div class="subheading"><h2>{{ request()->boolean('favorites') ? 'Favorites' : 'All titles' }}</h2><span>{{ $series->count() }} titles</span></div>
@if ($series->isEmpty())
    <section class="empty"><h2>{{ $stats['titles'] === 0 ? 'Your shelf starts here' : 'No titles found' }}</h2>
        <p>{{ $stats['titles'] === 0 ? 'Add your first title to get started.' : 'Try changing or clearing your filters.' }}</p>
        @if ($stats['titles'] === 0)<a class="button primary" href="{{ route('series.create') }}">Add a title</a>@else<a href="{{ route('library') }}">Clear filters</a>@endif
    </section>
@else
    <section class="series-list">
    @foreach ($series as $item)
        <article class="series-card">
            <a class="cover" href="{{ route('series.show', $item) }}">{{ mb_substr($item->title, 0, 1) }}</a>
            <div class="series-info">
                <div class="subheading"><div><a class="title" href="{{ route('series.show', $item) }}">{{ $item->title }}</a><p>{{ $item->author ?: 'Author not listed' }}</p></div>
                    <form method="post" action="{{ route('series.favorite', $item) }}">@csrf<button aria-label="Toggle favorite">{{ $item->is_favorite ? '★' : '☆' }}</button></form>
                </div>
                <p><span class="tag">{{ $item->media_type }}</span> <span class="tag">{{ $item->status }}</span></p>
                <p>{{ $item->current_chapter }}{{ $item->total_chapters ? ' / '.$item->total_chapters : '' }} chapters
                    @if ($item->completionPercentage() !== null) · {{ $item->completionPercentage() }}% complete @else · Ongoing @endif</p>
                @if ($item->total_chapters)<progress value="{{ $item->current_chapter }}" max="{{ $item->total_chapters }}"></progress>@endif
                <div class="actions"><a href="{{ route('series.show', $item) }}">Details</a>
                    @if ($item->status !== 'Completed' && ($item->total_chapters === null || $item->current_chapter < $item->total_chapters))
                        <form method="post" action="{{ route('series.increment', $item) }}">@csrf<button>+1 chapter</button></form>
                    @endif
                </div>
            </div>
        </article>
    @endforeach
    </section>
@endif
@endsection
