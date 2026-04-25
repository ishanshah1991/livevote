@extends('admin.layouts.app')

@section('title', 'Admin Registration')
@section('heading', 'Admin Registration')

@section('content')
    <form method="POST" action="{{ route('admin.register') }}">
        @csrf

        <label for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" maxlength="100" required autofocus>

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" minlength="8" required>

        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" minlength="8" required>

        <button type="submit">Create account</button>
    </form>
@endsection
