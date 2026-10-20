@extends('layouts.app')
@section('title', 'Manuscripts for review')
@section('main_class', 'wide')
@section('content')
@php
    $chip = fn ($f, $label = null) => $f
        ? '<a class="chip" href="'.e(route('files.show', $f)).'">'.e($label ?? ($f->original_name ?: basename($f->path))).'</a>'
        : ($label ? '<span class="chip off">'.e($label).'</span>' : '<span class="ms-date">—</span>');
@endphp
@forelse ($reviews as $r)
  @php $m = $r->manuscript; $files = $m->files; $mine = $files->where('slot', $r->slot); @endphp
  <article class="ms-card">
    <div class="ms-head">
      <span class="ms-code">{{ $m->code_label }}</span>
      <div class="ms-title">{{ $m->title }}</div>
      <div class="ms-meta">{{ $m->submitted_at?->format('d.m.Y') ?? $m->submitted_at_raw }}</div>
    </div>
    <div class="ms-row">
      <span class="ms-label">Received</span>
      <div class="ms-grow ms-actions">
        {!! $chip($files->firstWhere('type', 'manuscript'), 'Manuscript') !!}
        @foreach ($files->whereIn('type', ['photo', 'graph'])->sortBy('slot') as $f)
          {!! $chip($f) !!}
        @endforeach
        <a class="chip" href="{{ route('reviews.help', 'checklist') }}">Checklist</a>
        <a class="chip" href="{{ route('reviews.help', 'instructions') }}">Instructions</a>
      </div>
    </div>
    <div class="ms-row">
      <span class="ms-label">Confirmed</span>
      <div class="ms-grow">
        @if ($r->confirmed_state === 'confirmed')
          <span class="badge ok">Yes{{ $r->confirmed_at ? ' · '.$r->confirmed_at->format('d.m.Y') : '' }}</span>
        @else
          <span class="badge warn">Not yet</span>
        @endif
        @if ($r->deadline_at) <small>&nbsp;Deadline: {{ $r->deadline_at->format('d.m.Y') }}</small> @endif
      </div>
    </div>
    <div class="ms-row">
      <span class="ms-label">Your review</span>
      <div class="ms-grow ms-actions">
        @if ($r->reviewed_at)
          {!! $chip($mine->firstWhere('type', 'review')) !!} {!! $chip($mine->firstWhere('type', 'checklist')) !!}
          <span class="ms-date">{{ $r->reviewed_at->format('d.m.Y') }}</span> <span class="badge">{{ $r->recommendation }}</span>
        @else <span class="ms-date">—</span> @endif
      </div>
      <a class="chip primary" href="{{ route('reviews.upload', $r) }}">Upload file</a>
    </div>
  </article>
@empty
  <p>You have no manuscripts for review.</p>
@endforelse
{{ $reviews->links('pagination::simple-default') }}
@endsection
