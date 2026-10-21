@extends('layouts.app')
@section('title', 'My manuscripts')
@section('main_class', 'wide')
@section('content')
@php
    $chip = fn ($f, $label = null) => $f
        ? '<a class="chip" href="'.e(route('files.show', $f)).'">'.e($label ?? ($f->original_name ?: basename($f->path))).'</a>'
        : '<span class="ms-date">—</span>';
    $one = fn ($files, $type) => $files->firstWhere('type', $type);
    $d = fn ($v) => $v ? $v->format('d.m.Y') : null;
@endphp
@forelse ($manuscripts as $m)
  @php $files = $m->files; @endphp
  <article class="ms-card">
    <div class="ms-head">
      <span class="ms-code">{{ $m->code_label }}</span>
      <div class="ms-title">{{ $m->title }}</div>
      <div class="ms-meta">{{ $m->submitted_at?->format('d.m.Y') ?? $m->submitted_at_raw }}</div>
    </div>
    <div class="ms-row">
      <span class="ms-label">Manuscript</span>
      <div class="ms-grow ms-actions">
        {!! $chip($one($files, 'manuscript')) !!}
        <details><summary>Cover Letter</summary><div>{!! nl2br(e($m->cover_letter)) !!}</div></details>
        <details><summary>Abstract</summary><div>{!! nl2br(e($m->abstract)) !!}</div></details>
      </div>
    </div>
    <div class="ms-row">
      <span class="ms-label">Figures</span>
      <div class="ms-grow ms-actions">
        @foreach (['photo' => 'Photo', 'graph' => 'Graph', 'corrected_photo' => 'Photo (corrected)', 'corrected_graph' => 'Graph (corrected)'] as $type => $label)
          @foreach ($files->where('type', $type)->sortBy('slot') as $f)
            {!! $chip($f, $label.' '.$f->slot) !!}
          @endforeach
        @endforeach
      </div>
      <a class="chip" href="{{ route('manuscripts.figures', $m) }}">Upload corrected Figures</a>
    </div>
    <div class="ms-row">
      <span class="ms-label">Corrected manuscript</span>
      <div class="ms-grow ms-actions">{!! $chip($one($files, 'corrected_manuscript')) !!} @if ($m->corrected_at)<span class="ms-date">{{ $d($m->corrected_at) }}</span>@endif</div>
      <a class="chip" href="{{ route('manuscripts.corrected', $m) }}">Upload corrected manuscript</a>
    </div>
    <div class="ms-row">
      <span class="ms-label">Editor's decision</span>
      <div class="ms-grow ms-actions">{!! $chip($one($files, 'editor_letter')) !!} @if ($m->editor_letter_at)<span class="ms-date">{{ $d($m->editor_letter_at) }}</span>@endif</div>
    </div>
    <div class="ms-row">
      <span class="ms-label">Proof file</span>
      <div class="ms-grow ms-actions">{!! $chip($one($files, 'proof')) !!} @if ($m->proof_at)<span class="ms-date">{{ $d($m->proof_at) }}</span>@endif</div>
    </div>
    <div class="ms-row">
      <span class="ms-label">Proof editor's letter</span>
      <div class="ms-grow ms-actions">{!! $chip($one($files, 'proof_letter')) !!} @if ($m->proof_editor_at)<span class="ms-date">{{ $d($m->proof_editor_at) }}</span>@endif</div>
    </div>
  </article>
@empty
  <p>You have no manuscripts yet. <a href="{{ route('manuscripts.create') }}">Submit a manuscript</a>.</p>
@endforelse
{{ $manuscripts->links() }}
@endsection
