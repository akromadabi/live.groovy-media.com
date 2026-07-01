<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
</head>

<body>
    <div class="app-container">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay"></div>

        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            @if(session()->has('impersonate'))
                <div style="background-color: #4f46e5; color: #ffffff; padding: 0.5rem 1.5rem; display: flex; align-items: center; justify-content: space-between; font-size: 0.875rem; border-bottom: 1px solid rgba(255,255,255,0.1); z-index: 101;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 1.25rem; height: 1.25rem; color: #c7d2fe;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Anda sedang login bypass sebagai <strong>{{ auth()->user()->name }}</strong>.</span>
                    </div>
                    <form action="{{ route('leave-impersonate') }}" method="POST" style="margin: 0; padding: 0;">
                        @csrf
                        <button type="submit" style="background-color: #ffffff; color: #4f46e5; border: none; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: background-color 0.15s ease-in-out;" onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='#ffffff'">
                            Kembali ke Admin
                        </button>
                    </form>
                </div>
            @endif

            <!-- Header -->
            @include('layouts.partials.header')

            <!-- Page Content -->
            <main class="page-content">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success animate-slide-up" data-auto-dismiss>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error animate-slide-up" data-auto-dismiss>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        <!-- Bottom Navigation (Mobile) -->
        @include('layouts.partials.bottom-nav')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        function confirmDelete(formId) {
            let message = 'Apakah Anda yakin ingin menghapus data ini?';
            if (formId.includes('user')) {
                message = 'Apakah Anda yakin ingin menghapus pengguna ini secara permanen?';
            } else if (formId.includes('file') || formId.includes('report')) {
                message = 'Apakah Anda yakin ingin menghapus file report Excel ini secara permanen? Semua data live yang terkait di dalamnya juga akan ikut terhapus.';
            } else if (formId.includes('attendance') || formId.includes('absen')) {
                message = 'Apakah Anda yakin ingin menghapus data absensi ini secara permanen?';
            }
            
            if (confirm(message)) {
                document.getElementById(formId).submit();
            }
        }
    </script>
    @stack('scripts')
</body>

</html>