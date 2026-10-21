@extends('layouts.app')
@section('title', 'Logs')
@section('main_class', 'wide')
@section('content')
{{ $logs->links() }}
<table class="ms">
  <tr><th>#</th><th>User</th><th>Date</th><th>Time</th><th>IP</th><th>Action</th></tr>
  @forelse ($logs as $l)
    @php $at = $l->logged_at ? \Illuminate\Support\Carbon::parse($l->logged_at) : null; @endphp
    <tr>
      <td>{{ $l->id }}.</td>
      <td>{{ $l->user }}</td>
      <td>{{ $at?->format('d.m.Y') }}</td>
      <td>{{ $at?->format('H:i:s') }}</td>
      <td>{{ $l->ip }}</td>
      <td>{{ $l->content }}</td>
    </tr>
  @empty
    <tr><td colspan="6">No log entries</td></tr>
  @endforelse
</table>
{{ $logs->links() }}
@endsection
