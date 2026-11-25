<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#F5F4F2">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'WadahNgopi' }}</title>
    <link rel="icon" href="/favicon-32x32.png" sizes="32x32">
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    @vite('resources/js/main.js')
</head>
<body>
    <div id="app" data-user="{{ auth()->id() }}" data-cms="{{ auth()->user()?->canAccessCms() ? '1' : '0' }}" data-error="{{ $errorPage ?? '' }}">
        <p role="status" class="app-boot-status">Memuat WadahNgopi…</p>
    </div>
    <noscript>Aktifkan JavaScript untuk membuka direktori WadahNgopi.</noscript>
</body>
</html>
