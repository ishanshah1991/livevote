@extends('admin.layouts.app')

@section('title', 'Polls')
@section('heading', 'My Polls')

@section('content')
    <div style="margin-bottom:1.25rem; display:flex; justify-content:space-between; align-items:center;">
        <span style="color:#6b7280;">{{ $polls->total() }} poll(s) total</span>
        <a href="{{ route('admin.polls.create') }}" style="padding:0.5rem 1rem; background:#2563eb; color:#fff; border-radius:4px; text-decoration:none;">+ New Poll</a>
    </div>

    @if (session('success'))
        <div class="status">{{ session('success') }}</div>
    @endif

    @if ($polls->isEmpty())
        <p style="color:#6b7280;">No polls yet. <a href="{{ route('admin.polls.create') }}">Create your first poll.</a></p>
    @else
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                <thead>
                    <tr style="background:#f3f4f6; text-align:left;">
                        <th style="padding:0.6rem 0.75rem; border-bottom:1px solid #e5e7eb;">Title</th>
                        <th style="padding:0.6rem 0.75rem; border-bottom:1px solid #e5e7eb;">Status</th>
                        <th style="padding:0.6rem 0.75rem; border-bottom:1px solid #e5e7eb;">Votes</th>
                        <th style="padding:0.6rem 0.75rem; border-bottom:1px solid #e5e7eb;">Created</th>
                        <th style="padding:0.6rem 0.75rem; border-bottom:1px solid #e5e7eb;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($polls as $poll)
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:0.6rem 0.75rem; font-weight:600;">{{ $poll->title }}</td>
                            <td style="padding:0.6rem 0.75rem;">
                                <span class="status-badge" style="
                                    padding:0.2rem 0.55rem;
                                    border-radius:9999px;
                                    font-size:0.75rem;
                                    font-weight:600;
                                    background:{{ $poll->is_active ? '#dcfce7' : '#f3f4f6' }};
                                    color:{{ $poll->is_active ? '#15803d' : '#6b7280' }};
                                ">{{ $poll->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td style="padding:0.6rem 0.75rem;">{{ $poll->votes_count }}</td>
                            <td style="padding:0.6rem 0.75rem; color:#6b7280;">{{ $poll->created_at->format('M d, Y') }}</td>
                            <td style="padding:0.6rem 0.75rem; white-space:nowrap; display:flex; gap:0.4rem; flex-wrap:wrap;">
                                <a href="{{ route('admin.polls.show', $poll) }}" style="padding:0.25rem 0.6rem; border:1px solid #2563eb; color:#2563eb; border-radius:4px; font-size:0.8rem; text-decoration:none;">View</a>
                                <a href="{{ route('admin.polls.results', $poll) }}" style="padding:0.25rem 0.6rem; border:1px solid #0891b2; color:#0891b2; border-radius:4px; font-size:0.8rem; text-decoration:none;">Results</a>
                                <button
                                    class="toggle-btn"
                                    data-poll-id="{{ $poll->id }}"
                                    data-is-active="{{ $poll->is_active ? '1' : '0' }}"
                                    style="padding:0.25rem 0.6rem; border:1px solid #d97706; color:#d97706; background:transparent; border-radius:4px; font-size:0.8rem; cursor:pointer;"
                                >{{ $poll->is_active ? 'Deactivate' : 'Activate' }}</button>
                                <button
                                    class="copy-link-btn"
                                    data-link="{{ route('admin.polls.show', $poll) }}"
                                    style="padding:0.25rem 0.6rem; border:1px solid #6b7280; color:#6b7280; background:transparent; border-radius:4px; font-size:0.8rem; cursor:pointer;"
                                >Copy Link</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.25rem;">
            {{ $polls->links() }}
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.querySelectorAll('.toggle-btn').forEach(function (btn) {
    btn.addEventListener('click', async function () {
        const pollId = this.dataset.pollId;
        const row = this.closest('tr');

        const res = await fetch('/admin/polls/' + pollId + '/toggle', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });

        const data = await res.json();

        if (data.success) {
            const badge = row.querySelector('.status-badge');
            if (data.is_active) {
                badge.textContent = 'Active';
                badge.style.background = '#dcfce7';
                badge.style.color = '#15803d';
                this.textContent = 'Deactivate';
            } else {
                badge.textContent = 'Inactive';
                badge.style.background = '#f3f4f6';
                badge.style.color = '#6b7280';
                this.textContent = 'Activate';
            }
            this.dataset.isActive = data.is_active ? '1' : '0';
        }
    });
});

document.querySelectorAll('.copy-link-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        navigator.clipboard.writeText(this.dataset.link).then(() => {
            const orig = this.textContent;
            this.textContent = 'Copied!';
            setTimeout(() => { this.textContent = orig; }, 2000);
        });
    });
});
</script>
@endpush
