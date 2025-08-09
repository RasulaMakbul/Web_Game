<!-- resources/views/components/layout.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fc;
        }

        .navbar {
            background-color: var(--info-color) !important;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .navbar-brand {
            font-weight: 700;
        }

        .wrapper {
            display: flex;
            width: 100%;
            min-height: calc(100vh - 56px);
        }

        .sidebar {
            width: 250px;
            min-height: calc(100vh - 56px);
            background: linear-gradient(180deg, var(--primary-color) 10%, #0d0e10 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s;
            position: fixed;
            top: 56px;
            left: 0;
            z-index: 100;
        }

        .sidebar .nav-item {
            position: relative;
        }

        .sidebar .nav-item .nav-link {
            display: block;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 0.25rem;
            margin: 0.25rem 0.5rem;
        }

        .sidebar .nav-item .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-item .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 700;
        }

        .sidebar .nav-item .nav-link i {
            margin-right: 0.5rem;
            width: 1.25rem;
            text-align: center;
        }

        .content-wrapper {
            flex: 1;
            margin-left: 250px;
            padding: 1.5rem;
            background-color: #f8f9fc;
            min-height: calc(100vh - 56px);
        }

        .card {
            position: relative;
            margin-bottom: 1.5rem;
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 700;
        }

        .game-card {
            transition: transform 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .game-card:hover {
            transform: translateY(-5px);
        }

        .game-thumbnail {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .game-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .game-card .card-footer {
            background-color: transparent;
            border-top: none;
        }

        .badge-3d {
            background-color: #6f42c1;
        }

        .footer {
            padding: 1rem;
            background-color: #f8f9fc;
            text-align: center;
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1511512578047-dfb367046420?ixlib=rb-4.0.3');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .pagination {
            margin-top: 2rem;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }

            .sidebar .nav-item .nav-link span {
                display: none;
            }

            .content-wrapper {
                margin-left: 80px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-controller"></i> GameHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('games.index') }}">Games</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="nav flex-column nav-pills p-3">
                <a class="nav-link {{ request()->is('games*') ? 'active' : '' }}" href="{{ route('games.index') }}">
                    <i class="bi bi-controller"></i>
                    <span>All Games</span>
                </a>
                <a class="nav-link {{ request()->is('games/top-rated') ? 'active' : '' }}"
                    href="{{ route('games.top-rated') }}">
                    <i class="bi bi-star"></i>
                    <span>Top Rated</span>
                </a>
                <a class="nav-link {{ request()->is('games/recent-releases') ? 'active' : '' }}"
                    href="{{ route('games.recent-releases') }}">
                    <i class="bi bi-calendar3"></i>
                    <span>New Releases</span>
                </a>
                <a class="nav-link {{ request()->is('games/upcoming') ? 'active' : '' }}"
                    href="{{ route('games.upcoming') }}">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Upcoming</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            <div class="content">
                {{ $slot }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid">
            <span class="text-muted">© {{ date('Y') }} GameHub. All rights reserved.</span>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
