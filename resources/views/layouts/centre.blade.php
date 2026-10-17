<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Franchise Portal - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))</title>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --centre-navy: #1e1b4b;
            --centre-indigo: #312e81;
            --centre-accent: #f59e0b;
            --centre-teal: #0d9488;
            --centre-text-dark: #0f172a;
            --centre-text-muted: #64748b;
            --centre-bg-page: #f8fafc;
            --centre-card-bg: #ffffff;
            --centre-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--centre-bg-page);
            color: var(--centre-text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Header */
        .centre-header {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            box-shadow: 0 4px 15px rgba(30, 27, 75, 0.2);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .centre-code-badge {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.4);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .wallet-pill {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 5px 14px;
            color: #ffffff;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .wallet-pill:hover {
            background: rgba(255, 255, 255, 0.22);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.5);
        }

        /* Sub Navigation */
        .centre-subnav {
            background: #ffffff;
            border-bottom: 1px solid var(--centre-border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .centre-subnav .nav-link {
            color: var(--centre-text-muted);
            font-size: 0.875rem;
            font-weight: 600;
            padding: 12px 14px;
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .centre-subnav .nav-link:hover {
            color: var(--centre-indigo);
            background-color: #f1f5f9;
        }

        .centre-subnav .nav-link.active {
            color: var(--centre-indigo);
            border-bottom-color: var(--centre-indigo);
            background-color: #eef2ff;
        }

        /* Cards */
        .stat-card {
            border: 1px solid var(--centre-border);
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
        }

        /* Fixed Mobile Bottom Bar */
        .centre-mobile-bottom-bar {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid var(--centre-border);
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.06);
            z-index: 1040;
            padding: 6px 4px 8px 4px;
        }

        .centre-mobile-bottom-bar .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--centre-text-muted);
            font-size: 0.72rem;
            font-weight: 600;
            padding: 4px 6px;
            border-radius: 8px;
            transition: all 0.2s ease;
            flex: 1;
        }

        .centre-mobile-bottom-bar .bottom-nav-item i {
            font-size: 1.15rem;
            margin-bottom: 3px;
        }

        .centre-mobile-bottom-bar .bottom-nav-item.active {
            color: var(--centre-indigo);
            background-color: #eef2ff;
        }

        .centre-mobile-bottom-bar .bottom-nav-item.home-elevated {
            position: relative;
            top: -14px;
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            color: #ffffff;
            width: 52px;
            height: 52px;
            flex: 0 0 52px;
            border-radius: 50%;
            box-shadow: 0 6px 16px rgba(49, 46, 129, 0.35);
            border: 3px solid #ffffff;
        }

        .centre-mobile-bottom-bar .bottom-nav-item.home-elevated i {
            font-size: 1.25rem;
            margin-bottom: 0;
        }

        @media (max-width: 991.98px) {
            .centre-mobile-bottom-bar {
                display: flex;
            }
            body {
                padding-bottom: 74px;
            }
            .centre-subnav {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        /* Print styles */
        @media print {
            .centre-header, .centre-subnav, .centre-mobile-bottom-bar, .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding-bottom: 0 !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $authUser = Auth::user();
        $authCentre = $authUser->centre ?? ($authUser->isSuperAdmin() ? \App\Models\Centre::first() : null);
        $walletBalance = $authCentre?->wallet?->balance ?? 0.00;
    @endphp

    <!-- Top Header -->
    <header class="centre-header py-2 text-white">
        <div class="container-fluid px-3 px-lg-4">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Left: Branding & Centre Badge -->
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('centre.dashboard') }}" class="text-white text-decoration-none d-flex align-items-center gap-2">
                        <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" class="rounded bg-white p-1" style="height: 38px; width: auto; object-fit: contain;">
                        <div>
                            <div class="fw-bold fs-6 lh-1">{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}</div>
                            <span class="text-warning-emphasis" style="font-size: 0.72rem; letter-spacing: 0.5px; opacity: 0.85;">AUTHORIZED STUDY CENTRE PORTAL</span>
                        </div>
                    </a>

                    @if($authCentre)
                    <div class="d-none d-md-block ms-2 ps-3 border-start border-white-50">
                        <span class="centre-code-badge"><i class="fa-solid fa-code-branch me-1"></i>{{ $authCentre->centre_code }}</span>
                        <span class="small text-white-50 ms-2 fw-semibold">{{ $authCentre->centre_name }} ({{ $authCentre->city }})</span>
                    </div>
                    @endif
                </div>

                <!-- Right: Wallet, Notifications, User Menu -->
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <!-- Quick Wallet Balance Pill -->
                    <a href="{{ route('centre.wallet.index') }}" class="wallet-pill" title="Franchise Wallet Balance">
                        <i class="fa-solid fa-wallet text-warning"></i>
                        <span class="d-none d-sm-inline fw-semibold text-white-50">Wallet:</span>
                        <span class="fw-bold text-white">₹{{ number_format($walletBalance, 2) }}</span>
                    </a>

                    <!-- Centre Director User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light rounded-pill px-3 dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-tie text-warning"></i>
                            <span class="d-none d-md-inline fw-semibold">{{ $authCentre->director_name ?? $authUser->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2 mt-2" style="min-width: 230px;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-dark">{{ $authCentre->centre_name ?? 'Study Centre' }}</div>
                                <div class="small text-muted">{{ $authUser->email }}</div>
                                @if($authCentre)
                                <span class="badge bg-primary-subtle text-primary font-monospace mt-1">{{ $authCentre->centre_code }}</span>
                                @endif
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('centre.profile.show') }}">
                                    <i class="fa-solid fa-id-card-clip text-primary me-2"></i>Centre Profile & Details
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('centre.downloads.certificate') }}">
                                    <i class="fa-solid fa-certificate text-warning me-2"></i>Affiliation Certificate
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('centre.support.index') }}">
                                    <i class="fa-solid fa-headset text-info me-2"></i>Head Office Support
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Sub Navigation Bar (Desktop & Tablet) -->
    <nav class="centre-subnav">
        <div class="container-fluid px-3 px-lg-4">
            <div class="d-flex align-items-center justify-content-between overflow-x-auto py-1">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('centre.dashboard') ? 'active' : '' }}" href="{{ route('centre.dashboard') }}">
                            <i class="fa-solid fa-chart-pie"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('centre.students.*') ? 'active' : '' }}" href="{{ route('centre.students.index') }}">
                            <i class="fa-solid fa-user-graduate"></i> Students & Admissions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('centre.batches.*') ? 'active' : '' }}" href="{{ route('centre.batches.index') }}">
                            <i class="fa-solid fa-users"></i> Batches & Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('centre.exams.*') ? 'active' : '' }}" href="{{ route('centre.exams.index') }}">
                            <i class="fa-solid fa-clipboard-list"></i> Exams & Marks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('centre.fees.*') ? 'active' : '' }}" href="{{ route('centre.fees.index') }}">
                            <i class="fa-solid fa-receipt"></i> Fee Collections
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('centre.wallet.*') ? 'active' : '' }}" href="{{ route('centre.wallet.index') }}">
                            <i class="fa-solid fa-wallet"></i> Wallet & Top-up
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('centre.downloads.*') ? 'active' : '' }}" href="{{ route('centre.downloads.index') }}">
                            <i class="fa-solid fa-folder-open"></i> Resources & Affiliation
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('centre.support.*') ? 'active' : '' }}" href="{{ route('centre.support.index') }}">
                            <i class="fa-solid fa-headset"></i> Support Desk
                        </a>
                    </li>
                </ul>

                <div class="d-none d-xl-flex align-items-center gap-2">
                    <a href="{{ route('centre.students.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                        <i class="fa-solid fa-user-plus me-1"></i>New Admission
                    </a>
                    <a href="{{ route('centre.fees.create') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                        <i class="fa-solid fa-indian-rupee-sign me-1"></i>Collect Fee
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow-1 py-4">
        <div class="container-fluid px-3 px-lg-4">
            <!-- Flash Notifications -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-circle-check fs-5"></i>
                <div class="flex-grow-1 fw-medium">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                <div class="flex-grow-1 fw-medium">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-circle-exclamation fs-5"></i>
                <div class="flex-grow-1 fw-medium">{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="fa-solid fa-circle-xmark me-1"></i>Please correct the following errors:</div>
                <ul class="mb-0 small ps-3">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Fixed Mobile Bottom Navigation Bar -->
    <nav class="centre-mobile-bottom-bar">
        <a href="{{ route('centre.students.index') }}" class="bottom-nav-item {{ request()->routeIs('centre.students.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-graduate"></i>
            <span>Students</span>
        </a>
        <a href="{{ route('centre.batches.index') }}" class="bottom-nav-item {{ request()->routeIs('centre.batches.*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-check"></i>
            <span>Attendance</span>
        </a>
        <a href="{{ route('centre.dashboard') }}" class="bottom-nav-item home-elevated {{ request()->routeIs('centre.dashboard') ? 'active' : '' }}" title="Dashboard Home">
            <i class="fa-solid fa-house"></i>
        </a>
        <a href="{{ route('centre.fees.index') }}" class="bottom-nav-item {{ request()->routeIs('centre.fees.*') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt"></i>
            <span>Fees</span>
        </a>
        <a href="{{ route('centre.wallet.index') }}" class="bottom-nav-item {{ request()->routeIs('centre.wallet.*') ? 'active' : '' }}">
            <i class="fa-solid fa-wallet"></i>
            <span>Wallet</span>
        </a>
    </nav>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
