@extends('layouts.app')
@section('title', 'Proof')
@section('content')
<p class="ms-ref"><strong>{{ $manuscript->code_label }}</strong> – {{ $manuscript->title }}<br>Author: {{ $manuscript->author->first_name }} {{ $manuscript->author->last_name }} ({{ $manuscript->author->email }})</p>
<form method="post" action="{{ route('editor.proof', $manuscript) }}" enctype="multipart/form-data">
  @csrf
  <label for="proof">* Proof file:</label>
  <input type="file" name="proof" id="proof" required>
  <label for="proof_letter">* Proof editor's letter:</label>
  <input type="file" name="proof_letter" id="proof_letter" required>
  <p>Both files are attached to an e-mail to the author.</p>
  <button class="btn">Upload and send</button>
  <a class="btn ghost" href="{{ route('editor.list', 'inprogress') }}">Cancel</a>
</form>
@endsection
