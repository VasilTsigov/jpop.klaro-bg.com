@extends('layouts.app')
@section('title', 'Register')
@section('content')
<form method="post" action="{{ route('register') }}">@csrf
  <label>Email</label><input type="email" name="email" value="{{ old('email') }}">
  <label>Password</label><input type="password" name="password">
  <label>Confirm password</label><input type="password" name="password_confirmation">
  <label>First name</label><input type="text" name="first_name" value="{{ old('first_name') }}">
  <label>Last name</label><input type="text" name="last_name" value="{{ old('last_name') }}">
  <label>Address</label><input type="text" name="address" value="{{ old('address') }}">
  <label>City</label><input type="text" name="city" value="{{ old('city') }}">
  <label>Country</label><input type="text" name="country" value="{{ old('country') }}">
  <button class="btn">Register</button>
</form>
@endsection
