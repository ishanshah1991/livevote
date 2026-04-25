@extends('admin.layouts.app')

@section('title', 'Results – ' . $poll->title)
@section('heading', 'Results: ' . $poll->title)

@section('content')
    <div style="margin-bottom:1.25rem;">
        <a href="{{ route('admin.polls.show', $poll) }}" style="color:#2563eb; text-decoration:none; font-size:0.875rem;">← Back to Poll</a>
    </div>

    @php
        $total = $poll->options->sum('votes_count');
    @endphp

    <p style="color:#6b7280; font-size:0.875rem; margin-bottom:1.25rem;">
        Total votes: <strong style="color:#111827;">{{ $total }}</strong>
    </p>

    @if ($poll->options->isEmpty())
        <p style="color:#6b7280;">No options found for this poll.</p>
    @else
        <div style="display:flex; flex-direction:column; gap:1rem;" id="results-container">
            @foreach ($poll->options as $option)
                @php
                    $pct = $total > 0 ? round(($option->votes_count / $total) * 100, 1) : 0;
                @endphp
                <div class="result-row" data-option-id="{{ $option->id }}">
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.25rem; font-size:0.875rem;">
                        <span>{{ $option->option_text }}</span>
                        <span style="color:#6b7280;">
                            <span class="vote-count">{{ $option->votes_count }}</span> vote(s)
                            (<span class="vote-pct">{{ $pct }}</span>%)
                        </span>
                    </div>
                    <div style="background:#e5e7eb; border-radius:9999px; height:1.25rem; overflow:hidden;">
                        <div
                            class="bar"
                            style="
                                height:100%;
                                width:{{ $pct }}%;
                                background:#2563eb;
                                border-radius:9999px;
                                transition:width 0.4s ease;
                                min-width:{{ $pct > 0 ? '4px' : '0' }};
                            "
                        ></div>
                    </div>
                </div>
            @endforeach
        </div>

        <p style="margin-top:1.5rem; font-size:0.75rem; color:#9ca3af;">
            Real-time updates will be wired in Task 6 (WebSockets).
        </p>
    @endif
@endsection
