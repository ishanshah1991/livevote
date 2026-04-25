@extends('admin.layouts.app')

@section('title', 'Admin Login')
@section('heading', 'Admin Login')

@section('content')
    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <button type="submit">Log in</button>
    </form>
@endsection
