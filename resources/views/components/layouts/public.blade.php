@props(['title' => config('app.name')])
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="antialiased">
    {{ $slot }}
    @persist('toast') <flux:toast /> @endpersist
    @livewireScripts
</body>
</html>
