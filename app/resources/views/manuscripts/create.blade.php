@extends('layouts.app')
@section('title', 'Submit Manuscript')
@section('content')
<form method="post" action="{{ route('manuscripts.store') }}" enctype="multipart/form-data">
  @csrf
  <label for="cover_letter">* Cover Letter:</label>
  <textarea name="cover_letter" id="cover_letter" rows="6" required>{{ old('cover_letter') }}</textarea>

  <label for="title">* Full Title:</label>
  <textarea name="title" id="title" rows="2" required>{{ old('title') }}</textarea>

  <label for="abstract">* Abstract:</label>
  <textarea name="abstract" id="abstract" rows="8" required>{{ old('abstract') }}</textarea>

  <label for="keywords">* Keywords:</label>
  <textarea name="keywords" id="keywords" rows="2" required>{{ old('keywords') }}</textarea>

  <label for="comment">Comment:</label>
  <textarea name="comment" id="comment" rows="3">{{ old('comment') }}</textarea>

  <label for="manuscript">* File (.doc, .docx):</label>
  <input type="file" name="manuscript" id="manuscript" accept=".doc,.docx" required>

  <label>Photographs (up to {{ $photos }}):</label>
  @for ($i = 1; $i <= $photos; $i++)
    <div><input type="file" name="photo[{{ $i }}]" aria-label="Photograph {{ $i }}"></div>
  @endfor

  <label>Graphs (up to {{ $graphs }}):</label>
  @for ($i = 1; $i <= $graphs; $i++)
    <div><input type="file" name="graph[{{ $i }}]" aria-label="Graph {{ $i }}"></div>
  @endfor

  <button class="btn">Submit</button>
</form>
@endsection
