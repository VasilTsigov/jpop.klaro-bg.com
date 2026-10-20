<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Journal POP - @yield('title')</title>
<link rel="stylesheet" href="{{ asset('css/jpop.css') }}?v={{ filemtime(public_path('css/jpop.css')) }}">
</head>
<body>
@php
    $cur = fn ($url) => url()->current() === $url ? ' aria-current=page' : '';
@endphp
<header class="site-header"><div class="bar">
  <a class="brand" href="{{ url('/') }}"><strong>Journal POP</strong><small>Peer-reviewed journal</small></a>
  <nav class="nav" aria-label="Main">
    @auth
      <a href="{{ route('welcome') }}"{!! $cur(route('welcome')) !!}>Home</a>
      @if (in_array(auth()->user()->role, ['author', 'editor']))
        <a href="{{ route('manuscripts.create') }}"{!! $cur(route('manuscripts.create')) !!}>Submit Manuscripts</a>
        <a href="{{ route('manuscripts.index') }}"{!! $cur(route('manuscripts.index')) !!}>Manuscripts</a>
      @endif
      @if (auth()->user()->role === 'reviewer')
        <a href="{{ route('reviews.index') }}"{!! $cur(route('reviews.index')) !!}>Manuscripts for review</a>
      @endif
      @if (auth()->user()->role === 'editor')
        <a href="{{ route('editor.list', 'inprogress') }}"{!! $cur(route('editor.list', 'inprogress')) !!}>All Manuscripts</a>
        <a href="{{ route('editor.list', 'rejected') }}"{!! $cur(route('editor.list', 'rejected')) !!}>Rejected</a>
        <a href="{{ route('editor.list', 'published') }}"{!! $cur(route('editor.list', 'published')) !!}>Published</a>
        <a href="{{ route('editor.search') }}"{!! $cur(route('editor.search')) !!}>Search</a>
      @endif
      <span class="sep"></span>
      <a class="user" href="{{ route('profile') }}"{!! $cur(route('profile')) !!}>{{ auth()->user()->email }}</a>
      <form method="post" action="{{ route('logout') }}">@csrf<button>Log out</button></form>
    @else
      <a href="{{ route('login') }}"{!! $cur(route('login')) !!}>Login</a>
      <a href="{{ route('register') }}"{!! $cur(route('register')) !!}>Register</a>
    @endauth
    <span class="sep"></span>
    <a href="{{ route('instructions') }}"{!! $cur(route('instructions')) !!}>Instructions</a>
    <a href="{{ route('contacts') }}"{!! $cur(route('contacts')) !!}>Contact</a>
  </nav>
</div></header>
<main class="@yield('main_class')">
  <h2>@yield('title')</h2>
  @if (session('status'))<p class="ok">{{ session('status') }}</p>@endif
  @foreach ($errors->all() as $e)<p class="err">{{ $e }}</p>@endforeach
  @yield('content')
</main>
<footer class="site-footer">Journal POP</footer>
</body>
</html>
