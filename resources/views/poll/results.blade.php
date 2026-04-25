@extends('layouts.public')

@section('title', $poll->title . ' – Results')
@section('og_title', $poll->title . ' – Results')
@section('og_description', 'See the live results for "' . $poll->title . '"')

@section('content')
    <h1 style="font-size:1.5rem; font-weight:700; color:var(--clr-text); margin:0 0 0.375rem;">
        {{ $poll->title }}
    </h1>

    @if ($poll->description)
        <p style="color:var(--clr-text-muted); margin:0 0 1.25rem; font-size:0.9375rem; line-height:1.6;">
            {{ $poll->description }}
        </p>
    @endif

    @php $total = $poll->options->sum('votes_count'); @endphp

    <p style="margin:0 0 1.5rem;">
        <span id="total-votes-badge"
              style="display:inline-block; background:var(--clr-primary-bg); color:var(--clr-primary); font-size:0.8125rem; font-weight:600; padding:0.25rem 0.75rem; border-radius:var(--radius-full); border:1px solid var(--clr-primary-border);">
            {{ $total }} {{ $total === 1 ? 'vote' : 'votes' }}
        </span>
    </p>

    @if ($poll->options->isEmpty())
        <p style="color:var(--clr-text-muted);">No options found for this poll.</p>
    @else
        @include('poll._results_partial', ['poll' => $poll])
    @endif
@endsection
