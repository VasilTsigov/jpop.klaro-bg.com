@extends('layouts.app')
@section('title', 'Upload corrected figures')
@section('content')
<p class="ms-ref"><strong>{{ $manuscript->code_label }}</strong> – {{ $manuscript->title }}</p>
<form method="post" action="{{ route('manuscripts.figures', $manuscript) }}" enctype="multipart/form-data">
  @csrf
  <label>Corrected photographs:</label>
  @for ($i = 1; $i <= $photos; $i++)
    <div><input type="file" name="photo[{{ $i }}]" aria-label="Corrected photograph {{ $i }}"></div>
  @endfor

  <label>Corrected graphs:</label>
  @for ($i = 1; $i <= $graphs; $i++)
    <div><input type="file" name="graph[{{ $i }}]" aria-label="Corrected graph {{ $i }}"></div>
  @endfor

  <button class="btn">Upload</button>
</form>
@endsection
