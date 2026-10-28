@extends('layout')
@section('title', $series->exists ? 'Edit title' : 'Add title')
@section('content')
<section class="form-card wide">
    <a href="{{ $series->exists ? route('series.show', $series) : route('library') }}">← Back</a>
    <h1>{{ $series->exists ? 'Edit title' : 'Add a title' }}</h1>
    @if ($errors->any())<div class="error"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="post" action="{{ $series->exists ? route('series.update', $series) : route('series.store') }}">
        @csrf
        <label>Title<input name="title" value="{{ old('title', $series->title) }}" required></label>
        <label>Author<input name="author" value="{{ old('author', $series->author) }}"></label>
        <label>Format<select name="media_type">@foreach ($formats as $format)<option @selected(old('media_type', $series->media_type ?: 'Manga') === $format)>{{ $format }}</option>@endforeach</select></label>
        <label>Status<select name="status">@foreach ($statuses as $status)<option @selected(old('status', $series->status ?: 'Plan to Read') === $status)>{{ $status }}</option>@endforeach</select></label>
        <label>Current chapter<input type="number" name="current_chapter" min="0" value="{{ old('current_chapter', $series->current_chapter ?? 0) }}" required></label>
        <label>Total chapters <small>Leave blank if unknown or ongoing.</small><input type="number" name="total_chapters" min="1" value="{{ old('total_chapters', $series->total_chapters) }}"></label>
        <label>Rating<select name="rating"><option value="">Not rated</option>@for ($n=1; $n<=10; $n++)<option value="{{ $n }}" @selected((string) old('rating', $series->rating) === (string) $n)>{{ $n }} / 10</option>@endfor</select></label>
        <label>Review<textarea name="review_notes" rows="4">{{ old('review_notes', $series->review_notes) }}</textarea></label>
        <button class="primary">{{ $series->exists ? 'Save changes' : 'Add to library' }}</button>
    </form>
</section>
@endsection
