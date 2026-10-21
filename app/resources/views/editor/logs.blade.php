@extends('layouts.app')
@section('title', 'Logs')
@section('main_class', 'wide')
@section('content')
@php
    $labels = ['info' => 'info', 'warning' => 'warning', 'error' => 'error'];
    $size = fn ($b) => $b >= 1048576 ? round($b / 1048576, 1).' MB' : max(1, round($b / 1024)).' KB';
@endphp
<p class="role-filter">
  <a href="{{ route('editor.logs') }}">All</a>
  <a href="{{ route('editor.logs', ['level' => 'error']) }}">Errors</a>
  <a href="{{ route('editor.logs', ['category' => 'auth', 'level' => 'warning']) }}">Failed logins</a>
  <a href="{{ route('editor.logs', ['category' => 'auth']) }}">Sign-ins</a>
  <a href="{{ route('editor.logs', ['category' => 'upload']) }}">Uploads</a>
  <a href="{{ route('editor.logs', ['category' => 'upload', 'level' => 'warning']) }}">Refused uploads</a>
  <a href="{{ route('editor.logs', ['category' => 'security']) }}">Security</a>
</p>
<form method="get" action="{{ route('editor.logs') }}" class="log-filter">
  <select name="category" aria-label="Category">
    <option value="">All categories</option>
    @foreach ($categories as $key => $label)
      <option value="{{ $key }}" @selected(($f['category'] ?? null) === $key)>{{ $label }}</option>
    @endforeach
  </select>
  <select name="level" aria-label="Level">
    <option value="">All levels</option>
    @foreach ($labels as $key => $label)
      <option value="{{ $key }}" @selected(($f['level'] ?? null) === $key)>{{ $label }}</option>
    @endforeach
  </select>
  <input type="text" name="user" value="{{ $f['user'] ?? '' }}" placeholder="User (name or email)">
  <input type="number" name="manuscript" min="1" value="{{ $f['manuscript'] ?? '' }}" placeholder="Manuscript id">
  <input type="date" name="from" value="{{ $f['from'] ?? '' }}" aria-label="From">
  <input type="date" name="to" value="{{ $f['to'] ?? '' }}" aria-label="To">
  <input type="text" name="q" value="{{ $f['q'] ?? '' }}" placeholder="Text in the action">
  <button class="btn small">Filter</button>
  <a class="btn small" href="{{ route('editor.logs', array_merge(request()->query(), ['export' => 'csv', 'page' => null])) }}">Export CSV</a>
</form>
{{ $logs->links() }}
<table class="ms">
  <tr><th>#</th><th>Time</th><th>User</th><th>IP</th><th>Category</th><th>Action</th></tr>
  @forelse ($logs as $l)
    @php
      $at = $l->logged_at ? \Illuminate\Support\Carbon::parse($l->logged_at) : null;
      $ctx = $l->context ? json_decode($l->context, true) : null;
    @endphp
    <tr class="log-{{ $l->level ?? 'info' }}">
      <td>{{ $l->id }}.</td>
      <td>{{ $at?->format('d.m.Y H:i:s') }}</td>
      <td>{{ $l->user }}@if ($l->role) <small>({{ $l->role }})</small>@endif</td>
      <td>{{ $l->ip }}</td>
      <td><span class="badge {{ ($l->level ?? 'info') === 'error' ? 'bad' : (($l->level ?? 'info') === 'warning' ? 'warn' : '') }}">{{ $categories[$l->category] ?? $l->category }}</span></td>
      <td>
        {{ $l->content }}
        @if ($l->manuscript_id) <a href="{{ route('editor.logs', ['manuscript' => $l->manuscript_id]) }}" title="All entries of this manuscript"><small>[ms {{ $l->manuscript_id }}]</small></a>@endif
        @if ($l->url || $ctx)
          <details><summary>details</summary>
            <div>
              @if ($l->url) {{ $l->method }} {{ $l->url }}@if ($l->status) – HTTP {{ $l->status }}@endif<br>@endif
              @foreach (($ctx['files'] ?? []) as $file) File: {{ $file['name'] }} ({{ $size($file['size']) }})<br>@endforeach
              @foreach (collect($ctx ?? [])->except('files') as $k => $v) {{ $k }}: {{ is_array($v) ? implode('; ', array_map('strval', $v)) : $v }}<br>@endforeach
            </div>
          </details>
        @endif
      </td>
    </tr>
  @empty
    <tr><td colspan="6">No log entries</td></tr>
  @endforelse
</table>
{{ $logs->links() }}
@endsection
