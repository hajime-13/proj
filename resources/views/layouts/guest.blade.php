<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'OrderList') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); min-height: 100vh; }
        </style>
    </head>
    <body class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="w-100" style="max-width: 420px; padding: 1rem;">
            <div class="text-center mb-4">
                <a href="/" class="text-white text-decoration-none fs-4 fw-bold">📦 OrderList</a>
            </div>
            <div class="card border-0 shadow-lg rounded-3">
                <div class="card-body p-4">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
