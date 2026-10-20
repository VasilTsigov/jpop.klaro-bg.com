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
label{display:block;margin-top:12px;font-weight:bold}
input[type=text],input[type=email],input[type=password],textarea{width:100%;padding:6px;box-sizing:border-box}
.btn{margin-top:16px;padding:8px 18px;background:#1f3a5f;color:#fff;border:0;cursor:pointer}
.err{color:#b00020;margin:6px 0}.ok{color:#146c2e;margin:6px 0}
</style>
</head>
<body>
<header>
  <div><strong>Journal POP</strong></div>
  <nav>
    @auth
      <a href="{{ route('welcome') }}">Home</a>
      <a href="{{ route('profile') }}">{{ auth()->user()->email }}</a>
      <form method="post" action="{{ route('logout') }}">@csrf<button>Log out</button></form>
    @else
      <a href="{{ route('login') }}">Login</a>
      <a href="{{ route('register') }}">Register</a>
    @endauth
  </nav>
</header>
<main>
  <h2>@yield('title')</h2>
  @if (session('status'))<p class="ok">{{ session('status') }}</p>@endif
  @foreach ($errors->all() as $e)<p class="err">{{ $e }}</p>@endforeach
  @yield('content')
</main>
</body>
</html>
