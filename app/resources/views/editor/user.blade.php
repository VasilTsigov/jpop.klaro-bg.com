@extends('layouts.app')
@section('title', 'User '.$user->id)
@section('content')
<table class="ms">
  @foreach (['email' => 'Email', 'username' => 'Username', 'first_name' => 'First name', 'last_name' => 'Last name', 'phone' => 'Phone', 'fax' => 'Fax',
             'country' => 'Country', 'state' => 'State', 'city' => 'City', 'zip' => 'Zip', 'address' => 'Address', 'address2' => 'Address 2'] as $f => $label)
    <tr><td>{{ $label }}</td><td>{{ $user->$f }}</td></tr>
  @endforeach
  <tr><td>Registered</td><td>{{ $user->registered_at?->format('d.m.Y H:i') }}</td></tr>
  <tr><td>E-mail confirmed</td><td>{{ $user->email_verified_at ? 'yes' : 'no' }}</td></tr>
</table>
@if ($user->id !== auth()->id())
  <form method="post" action="{{ route('editor.user.role', $user) }}">
    @csrf
    <label for="role">Role:</label>
    <select name="role" id="role">
      @foreach ($roles as $key => $label)
        <option value="{{ $key }}" @selected($user->role === $key)>{{ $label }}</option>
      @endforeach
    </select>
    <button class="btn">Change Role</button>
  </form>
@else
  <p>Role: {{ $roles[$user->role] }} (you cannot change your own role)</p>
@endif
<p><a href="{{ route('editor.users') }}">Back to the list</a></p>
@endsection
