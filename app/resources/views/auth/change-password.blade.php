@extends('layouts.app')
@section('title', 'Change Password')
@section('content')
<form method="post" action="{{ route('profile.password') }}">@csrf
  <label>New Password</label><input type="password" name="password">
  <label>New Password again</label><input type="password" name="password_confirmation">
  <button class="btn">Change Password</button>
</form>
@endsection
