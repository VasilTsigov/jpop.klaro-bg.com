@extends('layouts.app')
@section('title', 'Welcome')
@section('content')
<p>Welcome, {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}.</p>
<p>Role: <strong>{{ auth()->user()->role }}</strong></p>
<p><a href="{{ route('profile') }}">Profile</a> · <a href="{{ route('profile.password') }}">Change password</a></p>
@endsection
