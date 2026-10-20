@extends('layouts.app')
@section('title', 'Upload review')
@section('content')
<p class="ms-ref"><strong>{{ $manuscript->code_label }}</strong> – {{ $manuscript->title }}</p>
<p>Reviewer: {{ $review->reviewer_name }}</p>
<form method="post" action="{{ route('reviews.upload', $review) }}" enctype="multipart/form-data">
  @csrf
  <label for="review_file">* Reviewed manuscript:</label>
  <input type="file" name="review_file" id="review_file" required>
  <label for="checklist_file">* Checklist completed:</label>
  <input type="file" name="checklist_file" id="checklist_file" required>
  <label for="recommendation">* Recommendation:</label>
  <select name="recommendation" id="recommendation" required>
    <option value="">Select recommendation ...</option>
    @foreach ($recommendations as $o)<option value="{{ $o }}" @selected(old('recommendation') === $o)>{{ $o }}</option>@endforeach
  </select>
  <button class="btn">Upload</button>
</form>
@endsection
