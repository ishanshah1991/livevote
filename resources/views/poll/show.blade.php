@extends('layouts.public')

@section('title', $poll->title)
@section('og_title', $poll->title)
@section('og_description', $poll->description ?? 'Cast your vote in this poll.')

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

    {{-- Already-voted inline message (revealed by JS on 409) --}}
    <div id="already-voted-msg"
         style="display:none; background:var(--clr-primary-bg); color:var(--clr-primary); padding:0.75rem 1rem; border-radius:var(--radius-md); margin-bottom:1.25rem; font-weight:500; border:1px solid var(--clr-primary-border);">
        You've already voted!
    </div>

    {{-- Voting form --}}
    <div id="voting-section"@if ($hasVoted) style="display:none;"@endif>
        <form id="vote-form" action="{{ route('poll.vote', $poll) }}" method="POST">
            @csrf
            <div style="display:flex; flex-direction:column; gap:0.75rem; margin-bottom:1.5rem;">
                @foreach ($poll->options as $option)
                    <label class="option-card"
                           data-option-id="{{ $option->id }}"
                           style="display:flex; align-items:center; gap:0.875rem; padding:1rem; background:var(--clr-surface); border:2px solid var(--clr-border); border-radius:var(--radius-md); cursor:pointer; transition:border-color 0.15s ease, background 0.15s ease;">
                        <input type="radio"
                               name="option_id"
                               value="{{ $option->id }}"
                               style="accent-color:var(--clr-primary); width:1.125rem; height:1.125rem; cursor:pointer; flex-shrink:0;">
                        <span style="font-size:0.9375rem; color:var(--clr-text);">{{ $option->option_text }}</span>
                    </label>
                @endforeach
            </div>

            <button type="submit"
                    id="submit-btn"
                    style="width:100%; padding:0.8rem 1rem; background:var(--clr-primary); color:#fff; font-size:1rem; font-weight:600; border:none; border-radius:var(--radius-md); cursor:pointer; transition:background 0.15s ease;"
                    onmouseover="if(!this.disabled)this.style.background='var(--clr-primary-hover)'"
                    onmouseout="if(!this.disabled)this.style.background='var(--clr-primary)'">
                Cast My Vote
            </button>
        </form>
    </div>

    {{-- Results section (hidden until vote cast, or shown immediately if already voted) --}}
    <div id="results-section"@if (!$hasVoted) style="display:none;"@endif>
        @if ($hasVoted)
            <div style="background:var(--clr-success-bg); color:var(--clr-success); padding:0.75rem 1rem; border-radius:var(--radius-md); margin-bottom:1.25rem; font-weight:500; border:1px solid var(--clr-success-border);">
                You've already voted — here are the current results.
            </div>
        @endif

        @include('poll._results_partial', ['poll' => $poll])

        <p style="margin-top:1.25rem;">
            <a href="{{ route('poll.results', $poll) }}"
               style="color:var(--clr-primary); font-size:0.875rem; text-decoration:none;">
                View full results page →
            </a>
        </p>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    var cards = document.querySelectorAll('.option-card');

    cards.forEach(function (card) {
        card.addEventListener('click', function () {
            var radio = card.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;

            cards.forEach(function (c) {
                c.style.borderColor = 'var(--clr-border)';
                c.style.background  = 'var(--clr-surface)';
            });

            card.style.borderColor = 'var(--clr-primary)';
            card.style.background  = 'var(--clr-primary-bg)';
        });
    });

    var form = document.getElementById('vote-form');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        var selected = form.querySelector('input[type="radio"]:checked');
        if (!selected) {
            alert('Please select an option before voting.');
            return;
        }

        var btn         = document.getElementById('submit-btn');
        var origText    = btn.textContent;
        btn.disabled    = true;
        btn.textContent = 'Submitting…';
        btn.style.background = '#93c5fd';

        try {
            var resp = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ option_id: parseInt(selected.value, 10) }),
            });

            var data = await resp.json();

            if (resp.status === 409 || (data && data.error === 'already_voted')) {
                fadeOut(document.getElementById('voting-section'));
                fadeIn(document.getElementById('already-voted-msg'));
                return;
            }

            if (!resp.ok) {
                throw new Error((data && data.message) || 'Something went wrong. Please try again.');
            }

            renderResults(data);
            fadeOut(document.getElementById('voting-section'));
            fadeIn(document.getElementById('results-section'));

        } catch (err) {
            btn.disabled    = false;
            btn.textContent = origText;
            btn.style.background = 'var(--clr-primary)';
            alert(err.message);
        }
    });

    function renderResults(data) {
        var badge = document.getElementById('total-votes-badge');
        if (badge) {
            badge.textContent = data.total + ' ' + (data.total === 1 ? 'vote' : 'votes');
        }

        var container = document.getElementById('poll-results-container');
        if (!container) return;

        var maxVotes = 0;
        data.results.forEach(function (opt) {
            if (opt.votes > maxVotes) maxVotes = opt.votes;
        });

        data.results.forEach(function (opt) {
            var row   = container.querySelector('[data-option-id="' + opt.id + '"]');
            if (!row) return;

            var countEl = row.querySelector('.vote-count');
            var pctEl   = row.querySelector('.vote-pct');
            var bar     = row.querySelector('.bar');
            var label   = row.querySelector('.option-label');

            if (countEl) countEl.textContent = opt.votes;
            if (pctEl)   pctEl.textContent   = opt.percentage;
            if (bar)     bar.style.width      = opt.percentage + '%';

            if (opt.votes > 0 && opt.votes === maxVotes) {
                row.style.background   = 'var(--clr-winner-bg)';
                row.style.borderColor  = 'var(--clr-winner-border)';
                if (label && !label.textContent.includes('🏆')) {
                    label.textContent = '🏆 ' + label.textContent;
                    label.style.fontWeight = '700';
                }
            }
        });
    }

    function fadeOut(el) {
        if (!el) return;
        el.style.transition = 'opacity 0.35s ease';
        el.style.opacity    = '0';
        setTimeout(function () { el.style.display = 'none'; }, 350);
    }

    function fadeIn(el) {
        if (!el) return;
        el.style.display = 'block';
        el.style.opacity = '0';
        el.style.transition = 'opacity 0.35s ease';
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { el.style.opacity = '1'; });
        });
    }
}());
</script>
@endpush
