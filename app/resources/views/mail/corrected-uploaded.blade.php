@extends('mail.layout')
@section('body')
Hi Ivan, <br/>
Author <strong>{{ $author->first_name }} {{ $author->last_name }}</strong>
has uploaded his corrected manuscript <strong>{{ $manuscript->code_label }}</strong> on {{ $manuscript->corrected_at?->format('d.m.Y') }}: <br/>
Title:  <strong>{{ $manuscript->title }}</strong><br/><br/>
Your Journal POP Team
@endsection
