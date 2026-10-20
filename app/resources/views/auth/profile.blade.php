@extends('layouts.app')
@section('title', 'Profile')
@section('content')
@php $u = auth()->user(); @endphp
<form method="post" action="{{ route('profile') }}">@csrf
  <p>Email: {{ $u->email }}</p>
  <label>First name</label><input type="text" name="first_name" value="{{ old('first_name', $u->first_name) }}">
  <label>Last name</label><input type="text" name="last_name" value="{{ old('last_name', $u->last_name) }}">
  <label>City</label><input type="text" name="city" value="{{ old('city', $u->city) }}">
  <label>Country</label><input type="text" name="country" value="{{ old('country', $u->country) }}">
  <label>Address</label><textarea name="address">{{ old('address', $u->address) }}</textarea>
  <p>Registered: {{ $u->registered_at }}</p>
  <button class="btn">Save</button>
</form>
<p><a href="{{ route('profile.password') }}">Change password</a></p>
@endsection
