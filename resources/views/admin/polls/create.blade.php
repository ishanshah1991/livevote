@extends('admin.layouts.app')

@section('title', 'Create Poll')
@section('heading', 'Create Poll')

@section('content')
    <form method="POST" action="{{ route('admin.polls.store') }}">
        @csrf

        <label for="title">Title <span style="color:#dc2626;">*</span></label>
        <input
            id="title"
            type="text"
            name="title"
            value="{{ old('title') }}"
            maxlength="255"
            required
            autofocus
        >

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            maxlength="2000"
            rows="3"
            style="width:100%; padding:0.5rem; margin-top:0.25rem; box-sizing:border-box;"
        >{{ old('description') }}</textarea>

        <label for="starts_at">Starts At</label>
        <input
            id="starts_at"
            type="datetime-local"
            name="starts_at"
            value="{{ old('starts_at') }}"
        >

        <label for="ends_at">Ends At</label>
        <input
            id="ends_at"
            type="datetime-local"
            name="ends_at"
            value="{{ old('ends_at') }}"
        >

        <fieldset style="border:1px solid #e5e7eb; border-radius:4px; padding:1rem; margin-top:1.25rem;">
            <legend style="font-weight:600; padding:0 0.5rem;">Options <span style="color:#dc2626;">*</span> <small style="color:#6b7280; font-weight:400;">(min 2, max 10)</small></legend>

            <div id="options-list">
                @php $oldOptions = old('options', ['', '']); @endphp
                @foreach ($oldOptions as $i => $val)
                    <div class="option-row" style="display:flex; gap:0.5rem; margin-bottom:0.5rem; align-items:center;">
                        <input
                            type="text"
                            name="options[]"
                            value="{{ $val }}"
                            maxlength="500"
                            placeholder="Option {{ $i + 1 }}"
                            required
                            style="flex:1; padding:0.45rem; box-sizing:border-box;"
                        >
                        <button type="button" class="remove-option-btn" style="padding:0.4rem 0.7rem; background:#fee2e2; color:#dc2626; border:none; border-radius:4px; cursor:pointer;">✕</button>
                    </div>
                @endforeach
            </div>

            <button type="button" id="add-option-btn" style="margin-top:0.5rem; padding:0.4rem 0.8rem; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; border-radius:4px; cursor:pointer;">+ Add Option</button>
        </fieldset>

        <div style="display:flex; gap:0.75rem; margin-top:1.5rem;">
            <button type="submit" style="padding:0.6rem 1.25rem; background:#2563eb; color:#fff; border:none; border-radius:4px; cursor:pointer;">Create Poll</button>
            <a href="{{ route('admin.polls.index') }}" style="padding:0.6rem 1.25rem; border:1px solid #6b7280; color:#374151; border-radius:4px; text-decoration:none;">Cancel</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
const list    = document.getElementById('options-list');
const addBtn  = document.getElementById('add-option-btn');
const MAX     = 10;

function updateRemoveButtons() {
    const rows = list.querySelectorAll('.option-row');
    rows.forEach(function (row) {
        row.querySelector('.remove-option-btn').disabled = rows.length <= 2;
    });
    addBtn.style.display = rows.length >= MAX ? 'none' : '';
}

addBtn.addEventListener('click', function () {
    const rows  = list.querySelectorAll('.option-row');
    if (rows.length >= MAX) return;

    const index = rows.length + 1;
    const div   = document.createElement('div');
    div.className = 'option-row';
    div.style = 'display:flex; gap:0.5rem; margin-bottom:0.5rem; align-items:center;';
    div.innerHTML = `
        <input type="text" name="options[]" maxlength="500" placeholder="Option ${index}" required
               style="flex:1; padding:0.45rem; box-sizing:border-box;">
        <button type="button" class="remove-option-btn"
                style="padding:0.4rem 0.7rem; background:#fee2e2; color:#dc2626; border:none; border-radius:4px; cursor:pointer;">✕</button>
    `;
    div.querySelector('.remove-option-btn').addEventListener('click', removeRow);
    list.appendChild(div);
    updateRemoveButtons();
});

list.querySelectorAll('.remove-option-btn').forEach(function (btn) {
    btn.addEventListener('click', removeRow);
});

function removeRow() {
    const rows = list.querySelectorAll('.option-row');
    if (rows.length <= 2) return;
    this.closest('.option-row').remove();
    updateRemoveButtons();
}

updateRemoveButtons();
</script>
@endpush
