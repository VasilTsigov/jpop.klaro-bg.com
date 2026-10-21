@extends('layouts.app')
@section('title', 'Users')
@section('main_class', 'wide')
@section('content')
<p class="role-filter">
  <a href="{{ route('editor.users') }}"@class(['cur' => ! $role])>All Users - {{ $counts->sum() }}</a>
  @foreach ($roles as $key => $label)
    <a href="{{ route('editor.users', ['role' => $key]) }}"@class(['cur' => $role === $key])>{{ $label }} - {{ $counts[$key] ?? 0 }}</a>
  @endforeach
</p>
<table class="ms">
  <tr><th>#</th><th>Email</th><th>Name</th><th>Location</th><th>Registered</th><th>Role</th></tr>
  @forelse ($users as $u)
    <tr>
      <td>{{ $u->id }}.</td>
      <td><a href="{{ route('editor.user', $u) }}">{{ $u->email }}</a></td>
      <td>{{ $u->first_name }} {{ $u->last_name }}</td>
      <td>{{ collect([$u->country, $u->city])->filter()->implode(', ') }}</td>
      <td>{{ $u->registered_at?->format('d.m.Y') }}</td>
      <td>{{ $roles[$u->role] ?? $u->role }}</td>
    </tr>
  @empty
    <tr><td colspan="6">No users</td></tr>
  @endforelse
</table>
{{ $users->links() }}
@endsection
