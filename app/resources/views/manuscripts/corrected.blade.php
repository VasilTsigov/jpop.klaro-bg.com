@extends('layouts.app')
@section('title', 'Upload corrected manuscript')
@section('content')
<p><strong>{{ $manuscript->code_label }}</strong> – {{ $manuscript->title }}</p>
<form method="post" action="{{ route('manuscripts.corrected', $manuscript) }}" enctype="multipart/form-data">
  @csrf
  <label for="corrected">* Corrected file:</label>
  <input type="file" name="corrected" id="corrected" required>
  <button class="btn">Upload</button>
</form>
@endsection
