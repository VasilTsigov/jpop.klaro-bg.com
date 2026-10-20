@extends('layouts.app')
@section('title', $title)
@section('main_class', 'wide')
@section('content')
@php
    // File as a pill; a missing file is shown dimmed so the slot stays visible.
    $chip = fn ($f, $label = null) => $f
        ? '<a class="chip" href="'.e(route('files.show', $f)).'">'.e($label ?? ($f->original_name ?: basename($f->path))).'</a>'
        : ($label ? '<span class="chip off">'.e($label).'</span>' : '<span class="ms-date">—</span>');
    $inProgress = $list === 'inprogress';
    $d = fn ($v) => $v ? $v->format('d.m.Y') : null;
@endphp
@forelse ($manuscripts as $m)
  @php
    $files = $m->files;
    $one = fn ($type, $slot = 1) => $files->where('type', $type)->where('slot', $slot)->first();
    $flag = $m->recommendation_color === '#FF0000';
  @endphp
  <article class="ms-card">
    <div class="ms-head">
      <a class="ms-code" href="{{ route('editor.status', $m) }}" @if ($flag) style="background:{{ $m->recommendation_color }};color:#fff" title="Rejected by two reviewers" @endif>{{ $m->code_label }}</a>
      <div class="ms-title">{{ $m->title }}</div>
      <div class="ms-meta"><strong>{{ $m->author->first_name }} {{ $m->author->last_name }}</strong><br>{{ $m->submitted_at?->format('d.m.Y') ?? $m->submitted_at_raw }}</div>
    </div>

    <div class="ms-row">
      <span class="ms-label">Files</span>
      <div class="ms-tools">
        {!! $chip($one('manuscript'), 'Manuscript') !!}
        <details><summary>Cover Letter</summary><div>{!! nl2br(e($m->cover_letter)) !!}</div></details>
        <details><summary>Abstract</summary><div>{!! nl2br(e($m->abstract)) !!}</div></details>
        <details><summary>Figures</summary><div>
          @php $any = false; @endphp
          @foreach (['photo' => 'Photo', 'graph' => 'Graph', 'corrected_photo' => 'Photo (corrected)', 'corrected_graph' => 'Graph (corrected)'] as $type => $label)
            @foreach ($files->where('type', $type)->sortBy('slot') as $f)
              @php $any = true; @endphp
              <div>{{ $label }} {{ $f->slot }}: {!! $chip($f) !!}</div>
            @endforeach
          @endforeach
          @unless ($any) — @endunless
        </div></details>
      </div>
    </div>

    @foreach ([1, 2, 3] as $slot)
      @php $r = $m->reviews->firstWhere('slot', $slot); $name = $r?->reviewer_name ?: 'Reviewer '.$slot; @endphp
      <div class="ms-row">
        <span class="slot" title="Reviewer {{ $slot }}">{{ $slot }}</span>
        <div class="ms-grow">
          @if ($inProgress && $m->author_id !== auth()->id())
            <a href="{{ route('editor.assign', [$m, $slot]) }}"><strong>{{ $name }}</strong></a>
          @elseif ($r?->reviewer_name)
            <strong>{{ $r->reviewer_name }}</strong>
          @else
            <span class="ms-date">{{ $name }}</span>
          @endif
          @if ($r?->invited_at) <small>&nbsp;invited {{ $d($r->invited_at) }}</small> @endif
        </div>
        <div class="ms-actions">
          @if ($r && $r->confirmed_state !== 'pending')
            <span class="badge {{ $r->confirmed_state === 'confirmed' ? 'ok' : 'bad' }}">{{ $r->confirmed_state === 'confirmed' ? 'Confirmed' : 'Not confirmed' }} {{ $d($r->confirmed_at) }}</span>
          @elseif ($r)
            <span class="badge warn">Waiting</span>
          @endif
          @if ($inProgress && $r && $r->reviewer_id)
            @if ($r->confirmed_state === 'pending')
              <form class="inline" method="post" action="{{ route('editor.resend', [$m, $slot]) }}">@csrf<button class="chip">Resend invitation</button></form>
            @else
              <a class="chip" href="{{ route('editor.reminder', [$m, $slot]) }}">Reminder</a>
            @endif
          @endif
          @if ($r)
            {!! $chip($files->firstWhere(fn ($f) => $f->type === 'checklist' && $f->slot === $slot), 'Checklist') !!}
            {!! $chip($files->firstWhere(fn ($f) => $f->type === 'review' && $f->slot === $slot), 'Review') !!}
            @if ($r->recommendation) <span class="badge">{{ $r->recommendation }}</span> @endif
          @endif
        </div>
      </div>
    @endforeach

    <div class="ms-row">
      <span class="ms-label">Editor's decision</span>
      <div class="ms-grow ms-actions">
        {!! $chip($one('editor_letter')) !!} @if ($m->editor_letter_at)<span class="ms-date">{{ $d($m->editor_letter_at) }}</span>@endif
        <a class="chip" href="{{ route('editor.decision', $m) }}">Decision comments</a>
      </div>
      @if ($inProgress) <a class="chip danger" href="{{ route('editor.reject', $m) }}">Reject Manuscript</a> @endif
    </div>
    <div class="ms-row">
      <span class="ms-label">Corrected manuscript</span>
      <div class="ms-grow ms-actions">{!! $chip($one('corrected_manuscript')) !!} @if ($m->corrected_at)<span class="ms-date">{{ $d($m->corrected_at) }}</span>@endif</div>
    </div>
    <div class="ms-row">
      <span class="ms-label">Proof</span>
      <div class="ms-grow ms-actions">
        {!! $chip($one('proof')) !!} @if ($m->proof_at)<span class="ms-date">{{ $d($m->proof_at) }}</span>@endif
        @if ($one('proof_letter')) <span class="ms-date">Editor's letter:</span> {!! $chip($one('proof_letter')) !!} @endif
      </div>
      <a class="chip" href="{{ route('editor.proof', $m) }}">Proof</a>
    </div>
  </article>
@empty
  <p>No manuscripts</p>
@endforelse
{{ $manuscripts->links('pagination::simple-default') }}
@endsection
