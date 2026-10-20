@extends('layouts.app')
@section('title', "Editor's Decision Comments")
@section('content')
<p><strong>{{ $manuscript->code_label }}</strong> – {{ $manuscript->title }}<br>Author: {{ $manuscript->author->first_name }} {{ $manuscript->author->last_name }} ({{ $manuscript->author->email }})</p>
<form method="post" action="{{ route('editor.decision', $manuscript) }}" enctype="multipart/form-data">
  @csrf
  <label for="letter">* Decision letter (doc, docx, pdf, rtf, odt):</label>
  <input type="file" name="letter" id="letter" required>
  <p>The letter is attached to an e-mail to the author.</p>
  <button class="btn">Upload and send</button>
  <a href="{{ route('editor.list', 'inprogress') }}">Cancel</a>
</form>
@endsection
