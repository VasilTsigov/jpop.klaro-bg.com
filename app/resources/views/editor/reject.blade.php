@extends('layouts.app')
@section('title', 'Reject Manuscript')
@section('content')
<p class="ms-ref"><strong>{{ $manuscript->code_label }}</strong> – {{ $manuscript->title }}</p>
<p>Author: {{ $manuscript->author->first_name }} {{ $manuscript->author->last_name }}<br>Email: {{ $manuscript->author->email }}</p>
<form method="post" action="{{ route('editor.reject', $manuscript) }}">
  @csrf
  <button class="btn danger">Reject Manuscript</button>
  <a class="btn ghost" href="{{ route('editor.list', 'inprogress') }}">Cancel</a>
</form>
@endsection
