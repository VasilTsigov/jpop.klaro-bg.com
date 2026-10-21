@extends('layouts.app')
@section('title', $manuscripts ? 'Search Result' : 'Search')
@section('main_class', 'wide')
@section('content')
<form method="get" action="{{ route('editor.search') }}">
  <p>Search in authors <em><strong>First Name</strong></em>, <em><strong>Last Name</strong></em>, <em><strong>First Name + Last Name</strong></em>
     and manuscripts <em><strong>Full Title</strong></em>, <em><strong>Abstract</strong></em>, <em><strong>Cover Letter</strong></em> and <em><strong>Keywords</strong></em></p>
  <textarea name="q" rows="3">{{ $q }}</textarea>
  <button class="btn">Search</button>
</form>
@if ($manuscripts)
  <hr>
  @forelse ($manuscripts as $m)
    <table class="ms">
      <tr>
        <th style="width:90px;background:{{ $m->recommendation_color }}"><a href="{{ route('editor.status', $m) }}">{{ $m->code_label }}</a></th>
        <th>{{ $m->title }}</th>
        <th>{{ $m->author->first_name }} {{ $m->author->last_name }}<br>{{ $m->submitted_at?->format('d.m.Y') ?? $m->submitted_at_raw }}</th>
      </tr>
      <tr><td><strong>Cover Letter:</strong></td><td colspan="2">{!! nl2br(e($m->cover_letter)) !!}</td></tr>
      <tr><td><strong>Abstract:</strong></td><td colspan="2">{!! nl2br(e($m->abstract)) !!}</td></tr>
      <tr><td><strong>Keywords:</strong></td><td colspan="2">{{ $m->keywords }}</td></tr>
    </table>
  @empty
    <p class="err">No search result</p>
  @endforelse
  {{ $manuscripts->links() }}
@endif
@endsection
