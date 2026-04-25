@php
    $total    = $poll->options->sum('votes_count');
    $maxVotes = $poll->options->max('votes_count');
@endphp

<div id="poll-results-container" style="display:flex; flex-direction:column; gap:0.75rem;">
    @foreach ($poll->options as $option)
        @php
            $pct      = $total > 0 ? round(($option->votes_count / $total) * 100, 1) : 0;
            $isWinner = $maxVotes > 0 && $option->votes_count === $maxVotes;
        @endphp
        <div class="result-row"
             data-option-id="{{ $option->id }}"
             style="padding:1rem; background:{{ $isWinner ? 'var(--clr-winner-bg, #fefce8)' : 'var(--clr-surface, #fff)' }}; border:1px solid {{ $isWinner ? 'var(--clr-winner-border, #fde047)' : 'var(--clr-border, #e5e7eb)' }}; border-radius:var(--radius-md, 0.5rem);">

            <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:0.5rem; gap:0.5rem;">
                <span class="option-label"
                      style="font-size:0.9375rem; font-weight:{{ $isWinner ? '700' : '500' }}; color:var(--clr-text, #111827);">
                    @if ($isWinner) 🏆 @endif{{ $option->option_text }}
                </span>
                <span style="white-space:nowrap; font-size:0.8125rem; color:var(--clr-text-muted, #6b7280);">
                    <span class="vote-count">{{ $option->votes_count }}</span> vote{{ $option->votes_count !== 1 ? 's' : '' }}
                    &nbsp;(<span class="vote-pct">{{ $pct }}</span>%)
                </span>
            </div>

            <div style="background:var(--clr-bar-track, #e5e7eb); border-radius:var(--radius-full, 9999px); height:0.875rem; overflow:hidden;">
                <div class="bar"
                     style="height:100%; width:{{ $pct }}%; background:var(--clr-bar-fill, #2563eb); border-radius:var(--radius-full, 9999px); transition:var(--transition-bar, width 0.5s ease); min-width:{{ $pct > 0 ? '4px' : '0' }};"></div>
            </div>
        </div>
    @endforeach
</div>
