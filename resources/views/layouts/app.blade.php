<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
<main class="mx-auto max-w-6xl px-4 py-10">
    {{ $slot }}
</main>
</body>
</html>
