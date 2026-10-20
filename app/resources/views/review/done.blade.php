@extends('layouts.app')
@section('title', 'Thank you')
@section('content')
@if ($action === 'agree')
  <p>You have <strong>confirmed</strong> to review the manuscript <strong>{{ $manuscript->code_label }}</strong>. Thank you! You can upload your review after you log in to the site.</p>
@else
  <p>You have <strong>declined</strong> to review the manuscript <strong>{{ $manuscript->code_label }}</strong>. Thank you for letting us know.</p>
@endif
@endsection
