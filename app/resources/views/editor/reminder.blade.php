@extends('layouts.app')
@section('title', "Reminder to reviewer $slot")
@section('content')
<p class="ms-ref"><strong>{{ $manuscript->code_label }}</strong> – {{ $manuscript->title }}<br>To: {{ $review->reviewer->first_name }} {{ $review->reviewer->last_name }} ({{ $review->reviewer->email }})</p>
<form method="post" action="{{ route('editor.reminder', [$manuscript, $slot]) }}">
  @csrf
  <textarea name="reminder" rows="18">{{ old('reminder', $text) }}</textarea>
  <button class="btn">Send reminder</button>
  <a class="btn ghost" href="{{ route('editor.list', 'inprogress') }}">Cancel</a>
</form>
@endsection
