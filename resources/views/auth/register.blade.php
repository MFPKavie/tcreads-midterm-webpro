@extends('layout')
@section('title', 'Register')
@section('content')
<section class="form-card">
    <h1>Create account</h1>
    <p>Start your personal reading library.</p>
    @if ($errors->any())<p class="error">{{ $errors->first() }}</p>@endif
    <form method="post" action="{{ route('register.store') }}">
        @csrf
        <label>Name<input name="name" value="{{ old('name') }}" required></label>
        <label>Email<input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Password<input type="password" name="password" minlength="8" required></label>
        <label>Confirm password<input type="password" name="password_confirmation" required></label>
        <button class="primary">Register</button>
    </form>
    <p>Already registered? <a href="{{ route('login') }}">Log in</a>.</p>
</section>
@endsection
