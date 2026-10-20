@extends('layouts.app')
@section('title', 'Change status')
@section('content')
<p><strong>{{ $manuscript->code_label }}</strong> – {{ $manuscript->title }}<br>Author: {{ $manuscript->author->first_name }} {{ $manuscript->author->last_name }}</p>
<form method="post" action="{{ route('editor.status', $manuscript) }}">
  @csrf
  <label for="status">Status:</label>
  <select name="status" id="status">
    <option value="1" @selected((int) $manuscript->status === 1)>Rejected</option>
    <option value="2" @selected((int) $manuscript->status === 2)>Published</option>
    <option value="0" @selected((int) $manuscript->status === 0)>All</option>
  </select>
  <button class="btn">Change Status</button>
</form>
<hr>
<h3>Letters to reviewers</h3>
@if ($letters)
  <p>Sent to each reviewer who gave a recommendation, with the editor's decision letter attached.</p>
  <form method="post" action="{{ route('editor.letters', $manuscript) }}">
    @csrf
    @foreach ($letters as $slot => $html)
      <label for="letter{{ $slot }}">Reviewer {{ $slot }}: {{ $manuscript->reviews->firstWhere('slot', $slot)->reviewer_name }}</label>
      <textarea name="letter[{{ $slot }}]" id="letter{{ $slot }}" rows="15">{{ $html }}</textarea>
    @endforeach
    <button class="btn">Send Letters to Reviewers</button>
  </form>
@else
  <p>No reviewer has given a recommendation yet.</p>
@endif
<p><a href="{{ route('editor.list', ['inprogress', 'rejected', 'published'][(int) $manuscript->status]) }}">Back to the list</a></p>
@endsection
