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

        <!-- Vite (Tailwind + Alpine) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { background-color: #f8f9fa; }
            .sidebar { min-height: 100vh; background: #1e293b; }
            .sidebar .nav-link { color: #cbd5e1; padding: .6rem 1.25rem; border-radius: .375rem; }
            .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #334155; color: #fff; }
            .sidebar .nav-link .bi { margin-right: .5rem; }
            .sidebar-brand { color: #fff; font-weight: 700; font-size: 1.2rem; padding: 1.25rem; display: block; text-decoration: none; border-bottom: 1px solid #334155; }
            .main-content { min-height: 100vh; }
            .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: .75rem 1.5rem; }
        </style>
    </head>
    <body>
        <div class="d-flex">
            <!-- Sidebar -->
            <nav class="sidebar d-flex flex-column" style="width:240px; flex-shrink:0;">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    📦 {{ config('app.name', 'OrderList') }}
                </a>
                <ul class="nav flex-column p-2 mt-2 flex-grow-1">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            📊 Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('orders.index') }}"
                           class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            🛒 Order List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}"
                           class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            👥 Users
                        </a>
                    </li>
                </ul>
                <div class="p-2 border-top border-secondary">
                    <a href="{{ route('profile.edit') }}"
                       class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        👤 {{ Auth::user()->name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                            🚪 Log Out
                        </button>
                    </form>
                </div>
            </nav>

            <!-- Main content -->
            <div class="flex-grow-1 main-content">
                <!-- Top bar -->
                @isset($header)
                <div class="topbar">
                    {{ $header }}
                </div>
                @endisset

                <!-- Page content -->
                <div class="p-4">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @include('layouts.partials.toasts')

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Toast auto-init -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-toast]').forEach((el) => {
                    if (!window.bootstrap || !bootstrap.Toast) return;
                    bootstrap.Toast.getOrCreateInstance(el, { delay: 3000 }).show();
                });
            });
        </script>
    </body>
</html>

