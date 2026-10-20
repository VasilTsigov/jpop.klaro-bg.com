@extends('mail.layout')
@section('body')
Hi Editor,<br/>
{{ $author->first_name }} {{ $author->last_name }} <br/>
has send and upload manuscript with title "{{ $manuscript->title }}" to the system
@endsection
