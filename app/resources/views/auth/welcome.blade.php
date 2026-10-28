@extends('layouts.app')
@section('title', 'Welcome')
@section('content')
@php $role = auth()->user()->role; @endphp
<p>Welcome, {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}.</p>
<p>Role: <span class="role">{{ $role }}</span></p>
<div class="cards">
  <a class="card" href="{{ route('manuscripts.create') }}"><strong>Submit Manuscripts</strong><span>Send a new manuscript</span></a>
  <a class="card" href="{{ route('manuscripts.index') }}"><strong>Manuscripts</strong><span>Your submissions</span></a>
  @if ($role === 'reviewer')
    <a class="card" href="{{ route('reviews.index') }}"><strong>Manuscripts for review</strong><span>Your assignments</span></a>
  @endif
  @if ($role === 'editor')
    <a class="card" href="{{ route('editor.list', 'inprogress') }}"><strong>All Manuscripts</strong><span>In progress</span></a>
    <a class="card" href="{{ route('editor.search') }}"><strong>Search</strong><span>By author or text</span></a>
  @endif
  <a class="card" href="{{ route('profile') }}"><strong>Profile</strong><span>Your details</span></a>
  <a class="card" href="{{ route('profile.password') }}"><strong>Change password</strong><span>Account security</span></a>
</div>
@endsection
