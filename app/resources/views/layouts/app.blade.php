<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Journal POP - @yield('title')</title>
<style>
body{font-family:Verdana,Arial,sans-serif;font-size:14px;margin:0;background:#f4f4f4;color:#222}
header{background:#1f3a5f;color:#fff;padding:12px 24px;display:flex;justify-content:space-between;align-items:center}
header a{color:#fff;margin-right:14px;text-decoration:none}
header form{display:inline}
header button{background:none;border:0;color:#fff;cursor:pointer;font:inherit;text-decoration:underline}
main{max-width:640px;margin:24px auto;background:#fff;padding:24px;border-radius:4px}
main.wide{max-width:1000px}
textarea{font:inherit}
table.ms{width:100%;border-collapse:collapse;margin-bottom:18px}
table.ms td,table.ms th{border:1px solid #ccc;padding:5px 8px;text-align:left;vertical-align:top}
table.ms th{background:#feed77}
label{display:block;margin-top:12px;font-weight:bold}
input[type=text],input[type=email],input[type=password],textarea{width:100%;padding:6px;box-sizing:border-box}
.btn{margin-top:16px;padding:8px 18px;background:#1f3a5f;color:#fff;border:0;cursor:pointer}
.btn.small{margin:0;padding:2px 8px}
textarea{width:100%;box-sizing:border-box}
.err{color:#b00020;margin:6px 0}.ok{color:#146c2e;margin:6px 0}
</style>
</head>
<body>
<header>
  <div><strong>Journal POP</strong></div>
  <nav>
    @auth
      <a href="{{ route('welcome') }}">Home</a>
      @if (auth()->user()->role === 'author')
        <a href="{{ route('manuscripts.create') }}">Submit Manuscripts</a>
        <a href="{{ route('manuscripts.index') }}">Manuscripts</a>
      @endif
      @if (auth()->user()->role === 'reviewer')
        <a href="{{ route('reviews.index') }}">Manuscripts for review</a>
      @endif
      @if (auth()->user()->role === 'editor')
        <a href="{{ route('editor.list', 'inprogress') }}">All Manuscripts</a>
        <a href="{{ route('editor.list', 'rejected') }}">Rejected</a>
        <a href="{{ route('editor.list', 'published') }}">Published</a>
      @endif
      <a href="{{ route('profile') }}">{{ auth()->user()->email }}</a>
      <form method="post" action="{{ route('logout') }}">@csrf<button>Log out</button></form>
    @else
      <a href="{{ route('login') }}">Login</a>
      <a href="{{ route('register') }}">Register</a>
    @endauth
  </nav>
</header>
<main class="@yield('main_class')">
  <h2>@yield('title')</h2>
  @if (session('status'))<p class="ok">{{ session('status') }}</p>@endif
  @foreach ($errors->all() as $e)<p class="err">{{ $e }}</p>@endforeach
  @yield('content')
</main>
</body>
</html>
