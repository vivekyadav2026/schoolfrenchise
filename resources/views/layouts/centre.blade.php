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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --centre-navy: #0f172a;
            --centre-navy-dark: #0a101f;
            --centre-navy-sidebar: #131b2e;
            --centre-indigo: #1e1b4b;
            --centre-indigo-light: #312e81;
            --centre-accent: #f59e0b;
            --centre-accent-hover: #d97706;
            --centre-cyan: #0284c7;
            --centre-cyan-light: #38bdf8;
            --centre-teal: #0d9488;
            --centre-text-dark: #0f172a;
            --centre-text-muted: #64748b;
            --centre-bg-page: #f8fafc;
            --centre-card-bg: #ffffff;
            --centre-border: #e2e8f0;
            --centre-sidebar-width: 270px;
            --centre-sidebar-collapsed-width: 76px;
            --centre-header-height: 64px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--centre-bg-page);
            color: var(--centre-text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        body.centre-sidebar-open {
            overflow: hidden;
        }

        /* Top Header */
        .centre-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #1e293b 100%);
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.25);
            position: sticky;
            top: 0;
            height: var(--centre-header-height);
            z-index: 1030;
            display: flex;
            align-items: center;
        }

        .centre-brand-title {
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: -0.3px;
            line-height: 1.1;
        }

        .centre-code-badge {
            background: rgba(245, 158, 11, 0.18);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.4);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.78rem;
            padding: 3px 9px;
            border-radius: 6px;
            font-weight: 700;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .wallet-pill {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 5px 14px;
            color: #ffffff;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 38px;
        }

        .wallet-pill:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.5);
            transform: translateY(-1px);
        }

        /* Persistent Franchise Sidebar Layout */
        .centre-wrapper {
            display: flex;
            flex: 1;
            min-height: calc(100vh - var(--centre-header-height));
            position: relative;
        }

        /* Sidebar Styling */
        .centre-sidebar {
            width: var(--centre-sidebar-width);
            background-color: var(--centre-navy-sidebar);
            color: #94a3b8;
            flex-shrink: 0;
            position: sticky;
            top: var(--centre-header-height);
            height: calc(100vh - var(--centre-header-height));
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 1040;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .centre-sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .centre-sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }
        .centre-sidebar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        .centre-sidebar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        /* Sidebar Centre Profile Card */
        .centre-sidebar-profile {
            padding: 16px;
            background: linear-gradient(180deg, rgba(30, 27, 75, 0.6) 0%, rgba(15, 23, 42, 0.6) 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .centre-avatar-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
            flex-shrink: 0;
        }

        .centre-sidebar-nav {
            padding: 12px 10px 30px;
            flex-grow: 1;
        }

        .centre-nav-heading {
            font-size: 0.68rem;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1px;
            color: #64748b;
            padding: 14px 14px 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .centre-sidebar .nav-link {
            color: #cbd5e1;
            font-size: 0.88rem;
            font-weight: 500;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 44px;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
        }

        .centre-sidebar .nav-link i {
            width: 22px;
            font-size: 1.05rem;
            color: #94a3b8;
            text-align: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .centre-sidebar .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
            transform: translateX(2px);
        }

        .centre-sidebar .nav-link:hover i {
            color: var(--centre-cyan-light);
        }

        .centre-sidebar .nav-link.active {
            color: #ffffff;
            background: linear-gradient(90deg, rgba(2, 132, 199, 0.25) 0%, rgba(30, 27, 75, 0.2) 100%);
            border-left: 3px solid var(--centre-cyan-light);
            font-weight: 700;
        }

        .centre-sidebar .nav-link.active i {
            color: var(--centre-cyan-light);
        }

        .centre-sidebar .nav-link .badge {
            margin-left: auto;
            font-size: 0.72rem;
            font-weight: 700;
        }

        /* Sidebar Footer */
        .centre-sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(10, 16, 31, 0.8);
            font-size: 0.8rem;
        }

        /* Collapsed Desktop State */
        body.sidebar-collapsed .centre-sidebar {
            width: var(--centre-sidebar-collapsed-width);
        }
        body.sidebar-collapsed .centre-sidebar .centre-sidebar-profile-text,
        body.sidebar-collapsed .centre-sidebar .centre-nav-heading,
        body.sidebar-collapsed .centre-sidebar .nav-link span,
        body.sidebar-collapsed .centre-sidebar .nav-link .badge,
        body.sidebar-collapsed .centre-sidebar .centre-sidebar-footer-text {
            display: none !important;
        }
        body.sidebar-collapsed .centre-sidebar .nav-link {
            justify-content: center;
            padding: 12px;
        }
        body.sidebar-collapsed .centre-sidebar .nav-link i {
            margin: 0;
            width: auto;
            font-size: 1.25rem;
        }
        body.sidebar-collapsed .centre-sidebar .centre-avatar-box {
            margin: 0 auto;
        }

        /* Content Area */
        .centre-content-wrapper {
            flex-grow: 1;
            min-width: 0;
            padding: 24px;
            background-color: var(--centre-bg-page);
        }

        /* Cards & Components */
        .stat-card {
            border: 1px solid var(--centre-border);
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
        }

        /* Mobile Backdrop */
        .centre-sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(2px);
            z-index: 1038;
            opacity: 0;
            transition: opacity 0.28s ease;
        }

        .centre-sidebar-backdrop.show {
            display: block;
            opacity: 1;
        }

        /* Mobile Fixed Bottom Navigation Bar */
        .centre-mobile-bottom-bar {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid var(--centre-border);
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08);
            z-index: 1035;
            padding: 6px 4px 8px;
            height: 64px;
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
            min-height: 48px;
        }

        .centre-mobile-bottom-bar .bottom-nav-item i {
            font-size: 1.15rem;
            margin-bottom: 2px;
        }

        .centre-mobile-bottom-bar .bottom-nav-item.active {
            color: var(--centre-cyan);
            background-color: #f0f9ff;
        }

        .centre-mobile-bottom-bar .bottom-nav-item.home-elevated {
            position: relative;
            top: -16px;
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: #ffffff;
            width: 52px;
            height: 52px;
            flex: 0 0 52px;
            border-radius: 50%;
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.4);
            border: 3px solid #ffffff;
        }

        .centre-mobile-bottom-bar .bottom-nav-item.home-elevated i {
            font-size: 1.3rem;
            margin-bottom: 0;
        }

        /* Responsive Media Queries */
        @media (max-width: 991.98px) {
            .centre-sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: calc(-1 * var(--centre-sidebar-width));
                height: 100vh;
                box-shadow: 6px 0 25px rgba(0, 0, 0, 0.35);
                z-index: 1045;
            }

            .centre-sidebar.show {
                transform: translateX(var(--centre-sidebar-width));
            }

            .centre-content-wrapper {
                padding: 16px 14px 84px 14px !important;
            }

            .centre-mobile-bottom-bar {
                display: flex;
            }
        }

        @media (max-width: 575.98px) {
            :root {
                --centre-sidebar-width: 280px;
                --centre-header-height: 58px;
            }
            .centre-content-wrapper {
                padding: 12px 10px 80px 10px !important;
            }
            .stat-card {
                border-radius: 10px;
            }
        }

        @media (max-width: 380px) {
            .centre-brand-title {
                font-size: 0.98rem;
            }
            .wallet-pill {
                padding: 4px 8px;
                font-size: 0.78rem;
            }
        }

        /* Print styles */
        @media print {
            .centre-header, .centre-sidebar, .centre-mobile-bottom-bar, .no-print, .centre-sidebar-backdrop {
                display: none !important;
            }
            .centre-content-wrapper {
                padding: 0 !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
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

    <!-- Top Header Bar -->
    <header class="centre-header px-3 px-lg-4 text-white">
        <div class="d-flex justify-content-between align-items-center w-100">
            <!-- Left: Sidebar Toggle, Institute Brand & Centre Code -->
            <div class="d-flex align-items-center gap-2 gap-md-3">
                <!-- Sidebar Hamburger Button (Desktop Collapsible & Mobile Drawer) -->
                <button class="btn btn-link text-white p-1 border-0" id="centreSidebarToggle" type="button" aria-label="Toggle Franchise Sidebar">
                    <i class="fa-solid fa-bars fs-4"></i>
                </button>

                <a href="{{ route('centre.dashboard') }}" class="text-white text-decoration-none d-flex align-items-center gap-2">
                    <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                         alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" 
                         class="rounded bg-white p-1" 
                         style="height: 38px; width: auto; object-fit: contain;">
                    <div class="d-none d-sm-block">
                        <div class="centre-brand-title text-white">
                            <span style="color: #38bdf8;">TECH</span> <span style="color: #fb923c;">ERA</span> COMPUTER
                        </div>
                        <div class="text-warning-emphasis fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">AUTHORIZED STUDY CENTRE PORTAL</div>
                    </div>
                </a>

                @if($authCentre)
                <div class="d-none d-md-flex align-items-center ms-2 ps-3 border-start border-white-50">
                    <span class="centre-code-badge"><i class="fa-solid fa-code-branch me-1"></i>{{ $authCentre->centre_code }}</span>
                    <span class="small text-white-50 ms-2 fw-semibold text-truncate" style="max-width: 220px;">{{ $authCentre->centre_name }}</span>
                </div>
                @endif
            </div>

            <!-- Right: Wallet Quick Top-up, User Menu & Shortcuts -->
            <div class="d-flex align-items-center gap-2 gap-sm-3">
                <!-- Quick Wallet Pill with Direct Top-up Shortcut -->
                <a href="{{ route('centre.wallet.index') }}" class="wallet-pill" title="Franchise Available Wallet Balance">
                    <i class="fa-solid fa-wallet text-warning"></i>
                    <span class="d-none d-sm-inline fw-semibold text-white-50">Wallet:</span>
                    <span class="fw-bold text-white">₹{{ number_format($walletBalance, 2) }}</span>
                </a>

                <!-- Quick Action Button (Desktop) -->
                <div class="d-none d-xl-flex align-items-center gap-2">
                    <a href="{{ route('centre.students.create') }}" class="btn btn-sm btn-info text-white rounded-pill px-3 fw-semibold">
                        <i class="fa-solid fa-user-plus me-1"></i>New Admission
                    </a>
                </div>

                <!-- Centre Director Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light rounded-pill px-3 dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-user-tie text-warning"></i>
                        <span class="d-none d-md-inline fw-semibold">{{ $authCentre->director_name ?? $authUser->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2 mt-2" style="min-width: 240px;">
                        <li class="px-3 py-2 border-bottom bg-light">
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
                            <a class="dropdown-item py-2" href="{{ route('centre.wallet.topup') }}">
                                <i class="fa-solid fa-plus-circle text-success me-2"></i>Recharge Wallet
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
    </header>

    <!-- WRAPPER: SIDEBAR + CONTENT -->
    <div class="centre-wrapper">
        <!-- Dark Backdrop for Mobile Slide-Out Drawer -->
        <div class="centre-sidebar-backdrop" id="centreSidebarBackdrop"></div>

        <!-- PERSISTENT FRANCHISE SIDEBAR -->
        <aside class="centre-sidebar" id="centreSidebar">
            <!-- Mobile Drawer Header with Close Button -->
            <div class="d-flex d-lg-none justify-content-between align-items-center p-3 border-bottom border-white-50 border-opacity-10">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                         alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" 
                         class="rounded bg-white p-1" 
                         style="height: 32px; width: auto;">
                    <div class="text-white fw-bold small">FRANCHISE MENU</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-light rounded-circle" id="centreSidebarCloseBtn" style="width: 32px; height: 32px; padding: 0;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Centre Info Card Header -->
            <div class="centre-sidebar-profile">
                <div class="d-flex align-items-center gap-3">
                    <div class="centre-avatar-box">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div class="centre-sidebar-profile-text overflow-hidden">
                        <div class="text-white fw-bold text-truncate" style="font-size: 0.95rem;">
                            {{ $authCentre->centre_name ?? 'Franchise Centre' }}
                        </div>
                        <div class="d-flex align-items-center gap-1 mt-1">
                            <span class="badge bg-warning text-dark font-monospace fw-bold" style="font-size: 0.72rem;">
                                {{ $authCentre->centre_code ?? 'DCE-CENTRE' }}
                            </span>
                            <span class="text-white-50 small text-truncate">{{ $authCentre->city ?? 'Bihar' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Wallet Quick Topup Block inside Sidebar -->
                <div class="centre-sidebar-profile-text mt-3 pt-2 border-top border-white-50 border-opacity-10 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50" style="font-size: 0.72rem;">WALLET BALANCE</div>
                        <div class="fw-bold text-warning font-monospace" style="font-size: 1.05rem;">
                            ₹{{ number_format($walletBalance, 2) }}
                        </div>
                    </div>
                    <a href="{{ route('centre.wallet.topup') }}" class="btn btn-xs btn-outline-warning text-warning py-1 px-2 rounded small" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-plus me-1"></i>Top-up
                    </a>
                </div>
            </div>

            <!-- Navigation Links List (21 Core Franchise Modules) -->
            <nav class="centre-sidebar-nav">
                <!-- Group 1: Core -->
                <div class="centre-nav-heading">
                    <i class="fa-solid fa-grip-vertical text-muted"></i>
                    <span>Core Management</span>
                </div>
                <a class="nav-link {{ request()->routeIs('centre.dashboard') ? 'active' : '' }}" href="{{ route('centre.dashboard') }}">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Group 2: Students & Admissions -->
                <div class="centre-nav-heading">
                    <i class="fa-solid fa-user-graduate text-muted"></i>
                    <span>Students & Admissions</span>
                </div>
                <a class="nav-link {{ request()->routeIs('centre.students.index') && !request()->has('filter') ? 'active' : '' }}" href="{{ route('centre.students.index') }}">
                    <i class="fa-solid fa-users"></i>
                    <span>All Students</span>
                </a>
                <a class="nav-link {{ request()->routeIs('centre.students.create') ? 'active' : '' }}" href="{{ route('centre.students.create') }}">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Direct Admission</span>
                </a>
                <a class="nav-link {{ request()->fullUrlIs(route('centre.students.index', ['filter' => 'admission_form'])) ? 'active' : '' }}" href="{{ route('centre.students.index', ['filter' => 'admission_form']) }}">
                    <i class="fa-solid fa-file-contract"></i>
                    <span>Admission Forms</span>
                </a>
                <a class="nav-link {{ request()->fullUrlIs(route('centre.students.index', ['filter' => 'id_card'])) ? 'active' : '' }}" href="{{ route('centre.students.index', ['filter' => 'id_card']) }}">
                    <i class="fa-solid fa-id-card-clip"></i>
                    <span>Digital ID Cards</span>
                </a>

                <!-- Group 3: Academics & Exams -->
                <div class="centre-nav-heading">
                    <i class="fa-solid fa-book-open text-muted"></i>
                    <span>Academics & Batches</span>
                </div>
                <a class="nav-link {{ request()->routeIs('centre.batches.index') && !request()->routeIs('centre.batches.attendance*') ? 'active' : '' }}" href="{{ route('centre.batches.index') }}">
                    <i class="fa-solid fa-users-rectangle"></i>
                    <span>Batch Schedules</span>
                </a>
                <a class="nav-link {{ request()->routeIs('centre.batches.attendance*') && !request()->routeIs('centre.batches.attendance.report') ? 'active' : '' }}" href="{{ route('centre.batches.index') }}#attendance">
                    <i class="fa-solid fa-clipboard-user"></i>
                    <span>Daily Attendance</span>
                </a>
                <a class="nav-link {{ request()->routeIs('centre.batches.attendance.report') ? 'active' : '' }}" href="{{ route('centre.batches.attendance.report') }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Attendance Reports</span>
                </a>

                <!-- Group 4: Examinations & Results -->
                <div class="centre-nav-heading">
                    <i class="fa-solid fa-pen-nib text-muted"></i>
                    <span>Examinations</span>
                </div>
                <a class="nav-link {{ request()->routeIs('centre.exams.index') ? 'active' : '' }}" href="{{ route('centre.exams.index') }}">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Exam Schedules</span>
                </a>
                <a class="nav-link {{ request()->routeIs('centre.exams.index') ? 'active' : '' }}" href="{{ route('centre.exams.index') }}#hallTickets">
                    <i class="fa-solid fa-ticket"></i>
                    <span>Hall Tickets</span>
                </a>
                <a class="nav-link {{ request()->routeIs('centre.exams.index') ? 'active' : '' }}" href="{{ route('centre.exams.index') }}#marks">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Marks Entry</span>
                </a>
                <a class="nav-link {{ request()->routeIs('centre.exams.results') ? 'active' : '' }}" href="{{ route('centre.exams.results') }}">
                    <i class="fa-solid fa-square-poll-vertical"></i>
                    <span>Exam Results</span>
                </a>

                <!-- Group 5: Academic Resources -->
                <div class="centre-nav-heading">
                    <i class="fa-solid fa-folder-open text-muted"></i>
                    <span>Resources & Certificates</span>
                </div>
                <a class="nav-link {{ request()->routeIs('centre.downloads.index') ? 'active' : '' }}" href="{{ route('centre.downloads.index') }}">
                    <i class="fa-solid fa-cloud-arrow-down"></i>
                    <span>Study Materials</span>
                </a>
                <a class="nav-link {{ request()->routeIs('centre.downloads.certificate') ? 'active' : '' }}" href="{{ route('centre.downloads.certificate') }}">
                    <i class="fa-solid fa-certificate"></i>
                    <span>Affiliation Certificate</span>
                </a>

                <!-- Group 6: Finance & Wallet -->
                <div class="centre-nav-heading">
                    <i class="fa-solid fa-indian-rupee-sign text-muted"></i>
                    <span>Finance & Accounts</span>
                </div>
                <a class="nav-link {{ request()->routeIs('centre.fees.index') && !request()->has('receipts') ? 'active' : '' }}" href="{{ route('centre.fees.index') }}">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Fee Collections</span>
                </a>
                <a class="nav-link {{ request()->routeIs('centre.fees.create') ? 'active' : '' }}" href="{{ route('centre.fees.create') }}">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>Collect Student Fee</span>
                </a>
                <a class="nav-link {{ request()->fullUrlIs(route('centre.fees.index', ['tab' => 'receipts'])) ? 'active' : '' }}" href="{{ route('centre.fees.index', ['tab' => 'receipts']) }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>Fee Receipts</span>
                </a>
                <a class="nav-link {{ request()->routeIs('centre.fees.dues') ? 'active' : '' }}" href="{{ route('centre.fees.dues') }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Outstanding Dues</span>
                </a>
                <a class="nav-link {{ request()->routeIs('centre.wallet.index') ? 'active' : '' }}" href="{{ route('centre.wallet.index') }}">
                    <i class="fa-solid fa-wallet"></i>
                    <span>Wallet & Ledger</span>
                </a>
                <a class="nav-link {{ request()->routeIs('centre.wallet.topup') ? 'active' : '' }}" href="{{ route('centre.wallet.topup') }}">
                    <i class="fa-solid fa-circle-plus"></i>
                    <span>Top-up Requests</span>
                </a>

                <!-- Group 7: Support & Operations -->
                <div class="centre-nav-heading">
                    <i class="fa-solid fa-headset text-muted"></i>
                    <span>Support & Desk</span>
                </div>
                <a class="nav-link {{ request()->routeIs('centre.support.index') ? 'active' : '' }}" href="{{ route('centre.support.index') }}">
                    <i class="fa-solid fa-ticket-simple"></i>
                    <span>Support Tickets</span>
                </a>
                <a class="nav-link {{ request()->routeIs('centre.support.create') ? 'active' : '' }}" href="{{ route('centre.support.create') }}">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Create Ticket</span>
                </a>

                <!-- Group 8: Account & Centre Profile -->
                <div class="centre-nav-heading">
                    <i class="fa-solid fa-gear text-muted"></i>
                    <span>Settings & Account</span>
                </div>
                <a class="nav-link {{ request()->routeIs('centre.profile.show') ? 'active' : '' }}" href="{{ route('centre.profile.show') }}">
                    <i class="fa-solid fa-building-user"></i>
                    <span>Centre Profile</span>
                </a>
                <a class="nav-link" href="{{ route('centre.profile.show') }}#passwordSection">
                    <i class="fa-solid fa-key"></i>
                    <span>Change Password</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="nav-link w-100 text-danger border-0 bg-transparent text-start">
                        <i class="fa-solid fa-right-from-bracket text-danger"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </nav>

            <!-- Sidebar Bottom Info -->
            <div class="centre-sidebar-footer">
                <div class="centre-sidebar-footer-text d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-white small fw-bold">TECH ERA ERP</div>
                        <div class="text-white-50" style="font-size: 0.72rem;">Helpline: 9162384660</div>
                    </div>
                    <span class="badge bg-success-subtle text-success font-monospace">ONLINE</span>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="centre-content-wrapper">
            <!-- Flash Notifications -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-circle-check fs-5 text-success"></i>
                <div class="flex-grow-1 fw-medium">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation fs-5 text-danger"></i>
                <div class="flex-grow-1 fw-medium">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-circle-exclamation fs-5 text-warning"></i>
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
        </main>
    </div>

    <!-- Fixed Mobile Bottom Navigation Bar (1-Thumb Thumbzone Access) -->
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
        <button type="button" class="bottom-nav-item border-0 bg-transparent" id="centreMobileBottomMenuTrigger" aria-label="Open Full Menu">
            <i class="fa-solid fa-bars-staggered"></i>
            <span>Menu</span>
        </button>
    </nav>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Franchise Panel Interactive JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('centreSidebar');
            const toggleBtn = document.getElementById('centreSidebarToggle');
            const closeBtn = document.getElementById('centreSidebarCloseBtn');
            const backdrop = document.getElementById('centreSidebarBackdrop');
            const bottomMenuTrigger = document.getElementById('centreMobileBottomMenuTrigger');

            // Restore desktop collapsed state from localStorage
            if (window.innerWidth >= 992) {
                if (localStorage.getItem('centre_sidebar_collapsed') === 'true') {
                    document.body.classList.add('sidebar-collapsed');
                }
            }

            function openMobileSidebar() {
                sidebar.classList.add('show');
                backdrop.classList.add('show');
                document.body.classList.add('centre-sidebar-open');
            }

            function closeMobileSidebar() {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
                document.body.classList.remove('centre-sidebar-open');
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    if (window.innerWidth >= 992) {
                        // Desktop collapsible toggle
                        document.body.classList.toggle('sidebar-collapsed');
                        const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                        localStorage.setItem('centre_sidebar_collapsed', isCollapsed ? 'true' : 'false');
                    } else {
                        // Mobile slide-out toggle
                        if (sidebar.classList.contains('show')) {
                            closeMobileSidebar();
                        } else {
                            openMobileSidebar();
                        }
                    }
                });
            }

            if (bottomMenuTrigger) {
                bottomMenuTrigger.addEventListener('click', function () {
                    openMobileSidebar();
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeMobileSidebar);
            }

            if (backdrop) {
                backdrop.addEventListener('click', closeMobileSidebar);
            }

            // Auto-close mobile drawer when any link is tapped
            sidebar.querySelectorAll('.centre-sidebar-nav a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 992) {
                        closeMobileSidebar();
                    }
                });
            });

            // Close on escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                    closeMobileSidebar();
                }
            });

            // Auto-dismiss alert notifications after 5 seconds
            setTimeout(function () {
                document.querySelectorAll('.alert.alert-dismissible:not(.alert-important)').forEach(function (alert) {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    if (bsAlert) bsAlert.close();
                });
            }, 5000);
        });
    </script>
    @stack('scripts')
</body>
</html>
