@extends('layouts.app')
@section('title', $title)
@section('main_class', 'wide')
@section('content')
@php
    $link = fn ($f, $label = null) => $f ? '<a href="'.e(route('files.show', $f)).'">'.e($label ?? ($f->original_name ?: basename($f->path))).'</a>' : null;
    $inProgress = $list === 'inprogress';
@endphp
@forelse ($manuscripts as $m)
  @php
    $files = $m->files;
    $one = fn ($type, $slot = 1) => $files->where('type', $type)->where('slot', $slot)->first();
  @endphp
  <table class="ms">
    <tr>
      <th style="width:90px;background:{{ $m->recommendation_color }}"><a href="{{ route('editor.status', $m) }}">{{ $m->code_label }}</a></th>
      <th colspan="4">{{ $m->title }}</th>
      <th>{{ $m->author->first_name }} {{ $m->author->last_name }}<br>{{ $m->submitted_at?->format('d.m.Y') ?? $m->submitted_at_raw }}</th>
      <th>[ {!! $link($one('manuscript'), 'Manuscript') ?? 'Manuscript' !!} ]</th>
    </tr>
    @foreach ([1, 2, 3] as $slot)
      @php $r = $m->reviews->firstWhere('slot', $slot); @endphp
      <tr>
        <td colspan="2">
          {{ $slot }}. [
          @if ($inProgress && $m->author_id !== auth()->id())
            <a href="{{ route('editor.assign', [$m, $slot]) }}">{{ $r?->reviewer_name ?: 'Reviewer '.$slot }}</a>
          @else
            {{ $r?->reviewer_name ?: 'Reviewer '.$slot }}
          @endif
          ]
        </td>
        <td>{{ $r?->invited_at?->format('d.m.Y') }}</td>
        <td>
          @if ($r && $r->confirmed_state !== 'pending')
            ( {{ $r->confirmed_state === 'confirmed' ? 'Confirmed' : 'Not confirmed' }} - {{ $r->confirmed_at?->format('d.m.Y') }} )
          @endif
          @if ($inProgress && $r && $r->reviewer_id)
            @if ($r->confirmed_state === 'pending')
              <form method="post" action="{{ route('editor.resend', [$m, $slot]) }}" style="display:inline">@csrf<button class="btn small">Resend invitation</button></form>
            @else
              [ <a href="{{ route('editor.reminder', [$m, $slot]) }}">Reminder</a> ]
            @endif
          @endif
        </td>
        <td>
          @if ($r)
            [ {!! $link($files->firstWhere(fn ($f) => $f->type === 'checklist' && $f->slot === $slot), 'Checklist') ?? 'Checklist' !!} ]
            [ {!! $link($files->firstWhere(fn ($f) => $f->type === 'review' && $f->slot === $slot), 'Review') ?? 'Review' !!} ]
          @endif
        </td>
        <td>{{ $r?->recommendation }}</td>
        <td>
          @if ($slot === 1) <details><summary>Cover Letter</summary>{!! nl2br(e($m->cover_letter)) !!}</details>
          @elseif ($slot === 2) <details><summary>Abstract</summary>{!! nl2br(e($m->abstract)) !!}</details>
          @else
            <details><summary>Figures</summary>
              @foreach (['photo' => 'Photo', 'graph' => 'Graph', 'corrected_photo' => 'Photo (corrected)', 'corrected_graph' => 'Graph (corrected)'] as $type => $label)
                @foreach ($files->where('type', $type)->sortBy('slot') as $f)
                  <div>{{ $label }} {{ $f->slot }}: {!! $link($f) !!}</div>
                @endforeach
              @endforeach
            </details>
          @endif
        </td>
      </tr>
    @endforeach
    <tr>
      <td colspan="3">
        <a href="{{ route('editor.decision', $m) }}">Editor's Decision Comments</a>:
        {!! $link($one('editor_letter')) !!} @if ($m->editor_letter_at) ( {{ $m->editor_letter_at->format('d.m.Y') }} ) @endif
      </td>
      <td colspan="4" style="text-align:right">
        @if ($inProgress) [ <a href="{{ route('editor.reject', $m) }}">Reject Manuscript</a> ] @endif
      </td>
    </tr>
    <tr>
      <td colspan="7">
        Corrected manuscript: {!! $link($one('corrected_manuscript')) !!}
        @if ($m->corrected_at) ( {{ $m->corrected_at->format('d.m.Y') }} ) @endif
      </td>
    </tr>
    <tr>
      <td colspan="7">
        <a href="{{ route('editor.proof', $m) }}">Proof</a>: {!! $link($one('proof')) !!}
        @if ($m->proof_at) ( {{ $m->proof_at->format('d.m.Y') }} ) @endif
        @if ($one('proof_letter')) &nbsp; Editor's letter: {!! $link($one('proof_letter')) !!} @endif
      </td>
    </tr>
  </table>
@empty
  <p>No manuscripts</p>
@endforelse
{{ $manuscripts->links('pagination::simple-default') }}
@endsection
