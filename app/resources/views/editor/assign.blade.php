@extends('layouts.app')
@section('title', "Reviewer $slot")
@section('content')
<p class="ms-ref"><strong>{{ $manuscript->code_label }}</strong> – {{ $manuscript->title }}</p>
<form method="post" action="{{ route('editor.assign', [$manuscript, $slot]) }}">
  @csrf
  <label for="reviewer_id">Reviewer:</label>
  <select name="reviewer_id" id="reviewer_id" required>
    <option value="">------- Select Reviewer ------</option>
    @foreach ($reviewers as $r)
      <option value="{{ $r->id }}" @selected(old('reviewer_id') == $r->id)>{{ $r->first_name }} {{ $r->last_name }}   ( {{ $r->email }} )</option>
    @endforeach
  </select>
  <p>An e-mail with the agree / decline links is sent to the reviewer.</p>
  <button class="btn">Send to reviewer</button>
  <a class="btn ghost" href="{{ route('editor.list', 'inprogress') }}">Cancel</a>
</form>
@endsection
