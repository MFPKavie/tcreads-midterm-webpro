<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TC Reads') - TC Reads</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<header>
    <a class="brand" href="{{ auth()->check() ? route('library') : route('login') }}">TC Reads</a>
    @auth
        <nav><a href="{{ route('library') }}">Library</a> <a href="{{ route('library', ['favorites' => 1]) }}">Favorites</a></nav>
        <span>{{ auth()->user()->name }}</span>
        <form method="post" action="{{ route('logout') }}">@csrf<button>Log out</button></form>
    @else
        <nav><a href="{{ route('login') }}">Log in</a> <a href="{{ route('register') }}">Register</a></nav>
    @endauth
</header>
<main>
    @if (session('message'))<p class="message">{{ session('message') }}</p>@endif
    @if ($errors->has('progress'))<p class="error">{{ $errors->first('progress') }}</p>@endif
    @yield('content')
</main>
<footer>TC Reads</footer>
</body>
</html>
