@extends('layouts.app')
@section('title', 'Review invitation')
@section('content')
@if (! $invitation)
  <p class="err">This link is no longer valid (it was already used, replaced by a newer one, or it has expired). Please contact the editorial office.</p>
@else
  <p>Manuscript <strong>{{ $manuscript->code_label }}</strong> – {{ $manuscript->title }}</p>
  <form method="post" action="{{ route('review.respond', request()->route('token')) }}">
    @csrf
    @if ($invitation->action === 'agree')
      <p>You <strong>confirm</strong> to review the manuscript.</p>
      <button class="btn">Confirm</button>
    @else
      <p>You <strong>decline</strong> to review the manuscript. You may add a message for the editors:</p>
      <textarea name="info" rows="5"></textarea>
      <button class="btn">Decline</button>
    @endif
  </form>
@endif
@endsection
