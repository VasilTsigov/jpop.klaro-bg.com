@extends('layouts.app')
@section('title', $heading)
@section('content')
<table>
  <tr><td><strong>No.:</strong></td><td>{{ $m->code_label }}</td></tr>
  <tr><td><strong>Title:</strong></td><td>{{ $m->title }}</td></tr>
  <tr><td><strong>Autor:</strong></td><td>{{ $m->author->first_name }} {{ $m->author->last_name }}</td></tr>
  <tr><td><strong>Date:</strong></td><td>{{ $m->submitted_at?->format('d.m.Y') ?? $m->submitted_at_raw }}</td></tr>
  <tr><td valign="top"><strong>{{ $heading }}:</strong></td><td>{!! nl2br(e($body)) !!}</td></tr>
</table>
<p><a href="javascript:history.back()">Go Back</a></p>
@endsection
