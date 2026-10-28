@extends('layout')
@section('title', $series->title)
@section('content')
<a href="{{ route('library') }}">← Back to library</a>
<section class="form-card wide">
    <p>{{ $series->media_type }} · {{ $series->status }}</p>
    <h1>{{ $series->title }}</h1>
    <p>By {{ $series->author ?: 'Unknown author' }}</p>
    <h2>Progress</h2>
    <p>{{ $series->current_chapter }}{{ $series->total_chapters ? ' / '.$series->total_chapters : '' }} chapters
        @if ($series->completionPercentage() !== null) · {{ $series->completionPercentage() }}% complete @else · Ongoing @endif
    </p>
    @if ($series->total_chapters)<progress value="{{ $series->current_chapter }}" max="{{ $series->total_chapters }}"></progress>@endif
    @if ($series->status !== 'Completed' && ($series->total_chapters === null || $series->current_chapter < $series->total_chapters))
        <form method="post" action="{{ route('series.increment', $series) }}">@csrf<button>+1 chapter</button></form>
    @endif
    <h2>Your rating</h2><p>{{ $series->rating ? $series->rating.' / 10' : 'Not rated' }}</p>
    <h2>Your review</h2><p>{{ $series->review_notes ?: 'No review yet.' }}</p>
    <p><a href="{{ route('series.edit', $series) }}">Edit title</a></p>
    <form method="post" action="{{ route('series.favorite', $series) }}">@csrf<button>{{ $series->is_favorite ? 'Remove favorite' : 'Add favorite' }}</button></form>
    <form method="post" action="{{ route('series.destroy', $series) }}" onsubmit="return confirm('Delete this title?')">@csrf<button class="danger">Delete title</button></form>
</section>
@endsection
