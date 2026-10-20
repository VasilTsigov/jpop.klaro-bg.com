@extends('layouts.app')
@section('title', 'Contacts')
@section('content')
<p><strong>Address:</strong><br>10, Kliment Ohridski blvd.<br>Sofia, Bulgaria
  (<a href="https://www.openstreetmap.org/search?query=10%20Kliment%20Ohridski%20blvd%2C%20Sofia" target="_blank" rel="noopener">map</a>)</p>
<h3>Contact Form</h3>
<form method="post" action="{{ route('contacts') }}">
  @csrf
  <label for="email">Email:</label>
  <input type="email" id="email" name="email" value="{{ old('email') }}" required>
  <label for="name">Name:</label>
  <input type="text" id="name" name="name" value="{{ old('name') }}" required>
  <label for="message">Message:</label>
  <textarea id="message" name="message" cols="60" rows="10" required>{{ old('message') }}</textarea>
  <div style="position:absolute;left:-9999px" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
  <button class="btn">Send</button>
</form>
@endsection
