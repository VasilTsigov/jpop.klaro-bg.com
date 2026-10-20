@extends('layouts.app')
@section('title', 'My manuscripts')
@section('main_class', 'wide')
@section('content')
@php
    $link = fn ($f) => $f ? '<a href="'.e(route('files.show', $f)).'">'.e($f->original_name ?: basename($f->path)).'</a>' : null;
    $one = fn ($files, $type) => $files->firstWhere('type', $type);
@endphp
@forelse ($manuscripts as $m)
  @php $files = $m->files; @endphp
  <table class="ms">
    <tr>
      <th style="width:110px">{{ $m->code_label }}</th>
      <th>{{ $m->title }}</th>
      <th style="width:130px">{{ $m->submitted_at?->format('d.m.Y') ?? $m->submitted_at_raw }}</th>
    </tr>
    <tr>
      <td>Manuscript</td>
      <td colspan="2">{!! $link($one($files, 'manuscript')) ?? '—' !!}</td>
    </tr>
    <tr>
      <td>Cover Letter</td>
      <td colspan="2"><details><summary>show</summary>{!! nl2br(e($m->cover_letter)) !!}</details></td>
    </tr>
    <tr>
      <td>Abstract</td>
      <td colspan="2"><details><summary>show</summary>{!! nl2br(e($m->abstract)) !!}</details></td>
    </tr>
    <tr>
      <td>Figures</td>
      <td colspan="2">
        @foreach (['photo' => 'Photo', 'graph' => 'Graph', 'corrected_photo' => 'Photo (corrected)', 'corrected_graph' => 'Graph (corrected)'] as $type => $label)
          @foreach ($files->where('type', $type)->sortBy('slot') as $f)
            <div>{{ $label }} {{ $f->slot }}: {!! $link($f) !!}</div>
          @endforeach
        @endforeach
        <div>[ <a href="{{ route('manuscripts.figures', $m) }}">Upload corrected Figures</a> ]</div>
      </td>
    </tr>
    <tr>
      <td>Corrected manuscript</td>
      <td colspan="2">
        {!! $link($one($files, 'corrected_manuscript')) !!}
        @if ($m->corrected_at) ( {{ $m->corrected_at->format('d.m.Y') }} ) @endif
        [ <a href="{{ route('manuscripts.corrected', $m) }}">Upload corrected manuscript</a> ]
      </td>
    </tr>
    <tr>
      <td>Editor's Decision Comments</td>
      <td colspan="2">
        {!! $link($one($files, 'editor_letter')) !!}
        @if ($m->editor_letter_at) ( {{ $m->editor_letter_at->format('d.m.Y') }} ) @endif
      </td>
    </tr>
    <tr>
      <td>Proof File</td>
      <td colspan="2">
        {!! $link($one($files, 'proof')) !!}
        @if ($m->proof_at) ( {{ $m->proof_at->format('d.m.Y') }} ) @endif
      </td>
    </tr>
    <tr>
      <td>Proof Editor's Letter</td>
      <td colspan="2">
        {!! $link($one($files, 'proof_letter')) !!}
        @if ($m->proof_editor_at) ( {{ $m->proof_editor_at->format('d.m.Y') }} ) @endif
      </td>
    </tr>
  </table>
@empty
  <p>You have no manuscripts yet. <a href="{{ route('manuscripts.create') }}">Submit a manuscript</a>.</p>
@endforelse
{{ $manuscripts->links('pagination::simple-default') }}
@endsection
