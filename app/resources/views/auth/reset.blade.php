@extends('layouts.app')
@section('title', 'Set a new password')
@section('main_class', 'narrow')
@section('content')
<form method="post" action="{{ route('password.update') }}">@csrf
  <input type="hidden" name="token" value="{{ $token }}">
  <label>Email</label><input type="email" name="email" value="{{ old('email', request('email')) }}">
  <label>New password</label><input type="password" name="password">
  <label>New password again</label><input type="password" name="password_confirmation">
  <button class="btn">Change password</button>
</form>
@endsection
