@extends('layouts.app')
@section('title', 'Manuscripts for review')
@section('main_class', 'wide')
@section('content')
@php $link = fn ($f) => $f ? '<a href="'.e(route('files.show', $f)).'">'.e($f->original_name ?: basename($f->path)).'</a>' : null; @endphp
@forelse ($reviews as $r)
  @php $m = $r->manuscript; $files = $m->files; $mine = $files->where('slot', $r->slot); @endphp
  <table class="ms">
    <tr>
      <th style="width:110px">{{ $m->code_label }}</th>
      <th>{{ $m->title }}</th>
      <th style="width:130px">{{ $m->submitted_at?->format('d.m.Y') ?? $m->submitted_at_raw }}</th>
      <th style="width:110px">[ <a href="{{ route('reviews.upload', $r) }}">Upload file</a> ]</th>
    </tr>
    <tr>
      <td>Received</td>
      <td colspan="3">
        [ {!! $link($files->firstWhere('type', 'manuscript')) ?? 'Manuscript' !!} ]
        @foreach ($files->whereIn('type', ['photo', 'graph'])->sortBy('slot') as $f)
          [ {!! $link($f) !!} ]
        @endforeach
        [ <a href="{{ route('reviews.help', 'checklist') }}">Checklist</a> ]
        [ <a href="{{ route('reviews.help', 'instructions') }}">Instructions</a> ]
      </td>
    </tr>
    <tr>
      <td>Confirmed</td>
      <td colspan="3">{{ $r->confirmed_state === 'confirmed' ? 'Yes'.($r->confirmed_at ? ' ('.$r->confirmed_at->format('d.m.Y').')' : '') : 'Not yet' }}
        @if ($r->deadline_at) &nbsp; Deadline: {{ $r->deadline_at->format('d.m.Y') }} @endif</td>
    </tr>
    <tr>
      <td>Your review</td>
      <td colspan="3">
        @if ($r->reviewed_at)
          {!! $link($mine->firstWhere('type', 'review')) !!} {!! $link($mine->firstWhere('type', 'checklist')) !!}
          ({{ $r->reviewed_at->format('d.m.Y') }}) – {{ $r->recommendation }}
        @else — @endif
      </td>
    </tr>
  </table>
@empty
  <p>You have no manuscripts for review.</p>
@endforelse
{{ $reviews->links('pagination::simple-default') }}
@endsection
