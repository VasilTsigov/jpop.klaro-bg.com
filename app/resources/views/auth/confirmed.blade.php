@extends('layouts.app')
@section('title', 'Email confirmed')
@section('content')
<p>Thank you for verifying your email address.</p>
<p><a href="{{ route('login') }}">Log in</a></p>
@endsection
