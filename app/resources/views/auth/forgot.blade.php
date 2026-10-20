@extends('layouts.app')
@section('title', 'Forgot password')
@section('content')
<form method="post" action="{{ route('password.email') }}">@csrf
  <label>Email</label><input type="email" name="email" value="{{ old('email') }}">
  <button class="btn">Send reset link</button>
</form>
@endsection
