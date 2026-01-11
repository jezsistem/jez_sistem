<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $data['title'] ?? 'JEZ PRO' }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Flowbite CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.3.0/dist/flowbite.min.css" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Fonts (CFT Icons) -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('app/assets/fonts/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app/assets/fonts/style-solid.css') }}" rel="stylesheet" type="text/css" />
    
    @stack('styles')
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .bg-login {
            background: #f8f8f8;
        }
        .sidebar-item:hover {
            background-color: #f3f4f6;
        }
        .sidebar-item.active {
            background-color: #f3f4f6;
            font-weight: 600;
        }
        .ml-72-plus {
            margin-left: 18.5rem;
        }
        .ml-32-plus {
            margin-left: 8.5rem;
        }

        
        /* Sidebar Toggle Transitions */
        #sidebar {
            transition: width 0.3s ease-in-out;
        }
        
        .sidebar-toggle-icon {
            transition: transform 0.3s ease-in-out;
        }
        
        .rotate-180 {
            transform: rotate(180deg);
        }
        
        .sidebar-menu-text,
        .sidebar-section-title,
        .sidebar-text-visible,
        .sidebar-icon-only {
            transition: opacity 0.2s ease-in-out;
        }
        
        /* Collapsed sidebar styles */
        .sidebar-collapsed .sidebar-item {
            justify-content: center !important;
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }
        
        .sidebar-collapsed .sidebar-menu-text,
        .sidebar-collapsed .sidebar-section-title,
        .sidebar-collapsed .sidebar-text-visible {
            display: none;
        }
        
        /* Tooltip for collapsed menu items */
        .sidebar-collapsed .sidebar-item {
            position: relative;
        }
        
        .sidebar-collapsed .sidebar-item:hover::after {
            content: attr(title);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 0.5rem;
            padding: 0.5rem 0.75rem;
            background-color: #1f2937;
            color: white;
            font-size: 0.875rem;
            white-space: nowrap;
            border-radius: 0.5rem;
            z-index: 50;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Main content transition */
        main {
            transition: margin-left 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-login">
    
    <!-- Top Bar -->
    @include('layouts.partials_v2.topbar')
    
    <div class="flex">
        <!-- Sidebar -->
        @include('layouts.partials_v2.sidebar')
        
        <!-- Main Content -->
        <main class="flex-1 px-6 pb-6 pt-2 ml-72">
            @yield('content')
        </main>
    </div>
    
    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>    
    
    <!-- DataTables (after jQuery) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <!-- Flowbite JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.3.0/dist/flowbite.min.js"></script>
    
    <!-- Chart.js (for charts) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @stack('scripts')
</body>
</html>

