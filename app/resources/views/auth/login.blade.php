@extends('layouts.app')
@section('title', 'Login')
@section('content')
<form method="post" action="{{ route('login') }}">@csrf
  <label>Email</label><input type="text" name="email" value="{{ old('email') }}" autofocus>
  <label>Password</label><input type="password" name="password">
  <button class="btn">Log in</button>
</form>
<p><a href="{{ route('password.request') }}">Forgot password?</a> · <a href="{{ route('register') }}">Register</a></p>
@endsection
