@extends('admin.layouts.app')

@section('title', $poll->title)
@section('heading', $poll->title)

@section('content')
    <div style="display:flex; gap:0.5rem; margin-bottom:1.5rem;">
        <a href="{{ route('admin.polls.index') }}" style="color:#2563eb; text-decoration:none; font-size:0.875rem;">← Back to Polls</a>
    </div>

    <dl style="display:grid; grid-template-columns:max-content 1fr; gap:0.5rem 1.25rem; font-size:0.9rem;">
        <dt style="font-weight:600; color:#6b7280;">Status</dt>
        <dd style="margin:0;">
            <span style="
                padding:0.2rem 0.55rem;
                border-radius:9999px;
                font-size:0.75rem;
                font-weight:600;
                background:{{ $poll->is_active ? '#dcfce7' : '#f3f4f6' }};
                color:{{ $poll->is_active ? '#15803d' : '#6b7280' }};
            ">{{ $poll->is_active ? 'Active' : 'Inactive' }}</span>
        </dd>

        @if ($poll->description)
            <dt style="font-weight:600; color:#6b7280;">Description</dt>
            <dd style="margin:0;">{{ $poll->description }}</dd>
        @endif

        <dt style="font-weight:600; color:#6b7280;">Starts</dt>
        <dd style="margin:0;">{{ $poll->starts_at ? $poll->starts_at->format('M d, Y H:i') : 'Immediately' }}</dd>

        <dt style="font-weight:600; color:#6b7280;">Ends</dt>
        <dd style="margin:0;">{{ $poll->ends_at ? $poll->ends_at->format('M d, Y H:i') : 'Never' }}</dd>

        <dt style="font-weight:600; color:#6b7280;">Created</dt>
        <dd style="margin:0;">{{ $poll->created_at->format('M d, Y H:i') }}</dd>
    </dl>

    <h2 style="font-size:1rem; margin-top:1.5rem;">Options</h2>
    <ol style="padding-left:1.25rem;">
        @foreach ($poll->options as $option)
            <li style="margin-bottom:0.35rem;">{{ $option->option_text }}</li>
        @endforeach
    </ol>

    <h2 style="font-size:1rem; margin-top:1.5rem;">Shareable Voting Link</h2>
    <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
        <input
            id="share-link"
            type="text"
            readonly
            value="{{ $shareableLink }}"
            style="flex:1; padding:0.45rem; border:1px solid #e5e7eb; border-radius:4px; background:#f9fafb; font-size:0.875rem;"
        >
        <button
            id="copy-link-btn"
            style="padding:0.45rem 0.9rem; background:#2563eb; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:0.875rem;"
        >Copy</button>
    </div>

    <div style="display:flex; gap:0.75rem; margin-top:1.75rem; flex-wrap:wrap;">
        <a href="{{ route('admin.polls.results', $poll) }}" style="padding:0.5rem 1rem; background:#0891b2; color:#fff; border-radius:4px; text-decoration:none; font-size:0.875rem;">View Results</a>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('copy-link-btn').addEventListener('click', function () {
    const input = document.getElementById('share-link');
    navigator.clipboard.writeText(input.value).then(() => {
        const orig = this.textContent;
        this.textContent = 'Copied!';
        setTimeout(() => { this.textContent = orig; }, 2000);
    });
});
</script>
@endpush
