@extends('layout')
@section('title', 'Log in')
@section('content')
<section class="form-card">
    <h1>Log in</h1>
    <p>Continue to your personal reading library.</p>
    @if ($errors->any())<p class="error">{{ $errors->first() }}</p>@endif
    <form method="post" action="{{ route('login.store') }}">
        @csrf
        <label>Email<input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
        <label>Password<input type="password" name="password" required></label>
        <button class="primary">Log in</button>
    </form>
    <p>New here? <a href="{{ route('register') }}">Create an account</a>.</p>
</section>
@endsection
