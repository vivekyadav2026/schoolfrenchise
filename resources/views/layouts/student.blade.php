<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal - ' . ($instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER'))</title>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --dce-primary: #5b21b6;
            --dce-primary-dark: #4c1d95;
            --dce-primary-light: #7c3aed;
            --dce-accent: #06b6d4;
            --dce-accent-green: #10b981;
            --dce-text-dark: #1e1b4b;
            --dce-text-muted: #64748b;
            --dce-bg-page: #f6f4fe;
            --dce-card-bg: #ffffff;
            --dce-border-subtle: #ede9fe;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--dce-bg-page);
            color: var(--dce-text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Violet Header */
        .student-header {
            background: linear-gradient(135deg, #4c1d95 0%, #6d28d9 100%);
            box-shadow: 0 4px 20px rgba(76, 29, 149, 0.15);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .student-brand-logo {
            height: 40px;
            object-fit: contain;
        }

        .student-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.4);
            font-size: 0.9rem;
        }

        /* Desktop Sub-Navbar */
        .student-subnav {
            background: #ffffff;
            border-bottom: 1px solid var(--dce-border-subtle);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .student-subnav .nav-link {
            color: #475569;
            font-weight: 500;
            font-size: 0.88rem;
            padding: 0.65rem 0.95rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .student-subnav .nav-link:hover {
            color: var(--dce-primary);
            background: #f5f3ff;
        }

        .student-subnav .nav-link.active {
            color: var(--dce-primary);
            background: #ede9fe;
            font-weight: 700;
        }

        /* Card Styles */
        .card {
            border: 1px solid var(--dce-border-subtle);
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(91, 33, 182, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 4px 16px rgba(91, 33, 182, 0.08);
        }

        /* Badge Customization */
        .badge-lavender {
            background-color: #ede9fe;
            color: var(--dce-primary);
            font-weight: 600;
        }

        .badge-success-subtle {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: 600;
        }

        .badge-danger-subtle {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: 600;
        }

        /* Fixed Mobile Bottom Navigation */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid var(--dce-border-subtle);
            box-shadow: 0 -4px 16px rgba(91, 33, 182, 0.08);
            z-index: 1040;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 0 0.5rem;
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #64748b;
            text-decoration: none;
            font-size: 0.72rem;
            font-weight: 500;
            flex: 1;
            padding: 0.4rem 0;
            position: relative;
            transition: color 0.2s ease;
        }

        .mobile-nav-item i {
            font-size: 1.25rem;
            margin-bottom: 2px;
        }

        .mobile-nav-item:hover,
        .mobile-nav-item.active {
            color: var(--dce-primary);
            font-weight: 700;
        }

        .mobile-nav-item.center-action {
            margin-top: -24px;
        }

        .mobile-nav-item.center-action .home-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6d28d9, #4c1d95);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(91, 33, 182, 0.4);
            border: 3px solid #ffffff;
            font-size: 1.3rem;
            margin-bottom: 2px;
        }

        /* Mobile Container Padding to prevent bottom nav overlap */
        @media (max-width: 767.98px) {
            .student-main-content {
                padding-bottom: 96px !important;
            }
        }

        /* Print formatting */
        @media print {
            .student-header,
            .student-subnav,
            .mobile-bottom-nav,
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .card {
                border: 1px solid #ccc !important;
                box-shadow: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $authUser = auth()->user();
        $authStudent = $authUser ? ($authUser->student ?? \App\Models\Student::where('user_id', $authUser->id)->first()) : null;
        if (!$authStudent && $authUser && $authUser->isSuperAdmin()) {
            $authStudent = \App\Models\Student::first();
        }

        $unreadCount = 0;
        if ($authUser) {
            $unreadCount = \App\Models\SystemNotification::where(function($q) use ($authUser) {
                $q->where('user_id', $authUser->id)->orWhereNull('user_id');
            })->whereNull('read_at')->count();
        }
    @endphp

    <!-- TOP VIOLET HEADER -->
    <header class="student-header py-2 text-white">
        <div class="container d-flex align-items-center justify-content-between">
            <!-- Left: Student Portal Brand Title -->
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('student.dashboard') }}" class="d-flex align-items-center gap-2 text-white text-decoration-none">
                    <span class="badge rounded-pill px-2.5 px-sm-3 py-1.5 fw-bold text-uppercase d-none d-sm-inline-block" style="background: rgba(255, 255, 255, 0.18); font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-graduation-cap me-1.5"></i> Student Portal
                    </span>
                    <span class="d-sm-none text-white fw-bold small p-1.5 rounded" style="background: rgba(255, 255, 255, 0.15);">
                        <i class="fa-solid fa-graduation-cap fs-5"></i>
                    </span>
                </a>
            </div>

            <!-- Center: Institute Logo & Name -->
            <div class="text-center px-1 px-sm-2 flex-grow-1">
                <a href="{{ route('student.dashboard') }}" class="d-inline-flex align-items-center gap-2 text-white text-decoration-none">
                    <img src="{{ $instituteSettings['logo_url'] ?? asset('images/tech-era-logo.png') }}" 
                         alt="{{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}" 
                         class="rounded-3 bg-white p-1 shadow-sm" 
                         style="height: 38px; width: auto; object-fit: contain;">
                    <div class="text-start d-none d-md-block">
                        <div class="fw-extrabold text-white text-uppercase" style="font-size: 0.95rem; letter-spacing: 0.8px; line-height: 1.1;">
                            {{ $instituteSettings['institute_name'] ?? 'TECH ERA COMPUTER' }}
                        </div>
                        <div class="text-white-50 small" style="font-size: 0.68rem; letter-spacing: 0.3px;">
                            {{ $instituteSettings['institute_tagline'] ?? 'Building Digital Skills for the Future' }}
                        </div>
                    </div>
                </a>
            </div>

            <!-- Right Controls: Notifications & Student Profile -->
            <div class="d-flex align-items-center gap-1 gap-sm-3 justify-content-end">
                <!-- Notification Icon -->
                <a href="{{ route('student.notifications') }}" class="text-white text-decoration-none position-relative p-2 rounded-circle hover-icon" title="Notifications">
                    <i class="fa-regular fa-bell fs-5"></i>
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.65rem;">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <!-- Student Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-white text-decoration-none dropdown-toggle" id="studentMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="student-avatar shadow-sm">
                            @if($authStudent && $authStudent->photo_path && file_exists(public_path('storage/' . $authStudent->photo_path)))
                                <img src="{{ asset('storage/' . $authStudent->photo_path) }}" alt="{{ $authStudent->full_name }}" class="w-100 h-100 rounded-circle object-fit-cover">
                            @else
                                {{ $authStudent ? strtoupper(substr($authStudent->full_name, 0, 1)) : 'S' }}
                            @endif
                        </div>
                        <div class="d-none d-lg-block text-start">
                            <div class="small fw-bold lh-1 text-white">{{ $authStudent ? Str::limit($authStudent->full_name, 15) : 'Student' }}</div>
                            <div class="text-white-50 font-monospace mt-0.5" style="font-size: 0.68rem;">{{ $authStudent->roll_no ?? 'ID Card' }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 py-2" aria-labelledby="studentMenuDropdown" style="min-width: 230px;">
                        <li class="px-3 py-2 border-bottom bg-light rounded-top-4">
                            <div class="fw-bold text-dark fs-6">{{ $authStudent->full_name ?? 'Student' }}</div>
                            <div class="small text-muted font-monospace">{{ $authStudent->roll_no ?? '' }}</div>
                            @if($authStudent && $authStudent->centre)
                                <div class="badge bg-primary-subtle text-primary rounded-pill small mt-1 font-sans">{{ $authStudent->centre->centre_name }}</div>
                            @endif
                        </li>
                        <li>
                            <a class="dropdown-item py-2 small" href="{{ route('student.profile') }}">
                                <i class="fa-solid fa-user me-2 text-primary"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 small" href="{{ route('student.id-card') }}">
                                <i class="fa-solid fa-id-card me-2 text-info"></i> Digital ID Card
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 small" href="{{ route('student.admission-form') }}">
                                <i class="fa-solid fa-file-contract me-2 text-secondary"></i> My Admission Form
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 small" href="{{ route('student.change-password') }}">
                                <i class="fa-solid fa-key me-2 text-warning"></i> Change Password
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 small text-danger fw-semibold">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- DESKTOP SUB-NAVBAR -->
    <nav class="student-subnav d-none d-md-block py-1">
        <div class="container d-flex align-items-center justify-content-between overflow-x-auto">
            <ul class="nav flex-nowrap py-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                        <i class="fa-solid fa-house me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.course*') ? 'active' : '' }}" href="{{ route('student.course') }}">
                        <i class="fa-solid fa-book-open me-1"></i> My Course
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.fees*') ? 'active' : '' }}" href="{{ route('student.fees') }}">
                        <i class="fa-solid fa-receipt me-1"></i> Fees & Payments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.study-materials*') ? 'active' : '' }}" href="{{ route('student.study-materials') }}">
                        <i class="fa-solid fa-folder-open me-1"></i> Study Materials
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.schedule*') ? 'active' : '' }}" href="{{ route('student.schedule') }}">
                        <i class="fa-solid fa-calendar-days me-1"></i> Schedule
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.attendance*') ? 'active' : '' }}" href="{{ route('student.attendance') }}">
                        <i class="fa-solid fa-clipboard-user me-1"></i> Attendance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.exams*') ? 'active' : '' }}" href="{{ route('student.exams') }}">
                        <i class="fa-solid fa-pen-nib me-1"></i> Exams & Hall Tickets
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.results*') ? 'active' : '' }}" href="{{ route('student.results') }}">
                        <i class="fa-solid fa-square-poll-vertical me-1"></i> Marksheets
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.certificates*') ? 'active' : '' }}" href="{{ route('student.certificates') }}">
                        <i class="fa-solid fa-award me-1"></i> Certificates
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.support*') ? 'active' : '' }}" href="{{ route('student.support') }}">
                        <i class="fa-solid fa-headset me-1"></i> Support
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- MAIN BODY CONTENT -->
    <main class="student-main-content py-4 flex-grow-1">
        <div class="container">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-circle-check fs-5 me-2 text-success"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-triangle-exclamation fs-5 me-2 text-danger"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="fw-bold mb-1"><i class="fa-solid fa-circle-exclamation me-2"></i>Please resolve the following errors:</div>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- MOBILE FIXED BOTTOM NAVIGATION -->
    <nav class="mobile-bottom-nav d-md-none" aria-label="Mobile Navigation">
        <!-- 1. Notifications -->
        <a href="{{ route('student.notifications') }}" class="mobile-nav-item {{ request()->routeIs('student.notifications*') ? 'active' : '' }}">
            <i class="fa-solid fa-bell position-relative">
                @if($unreadCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 8px; height: 8px;"></span>
                @endif
            </i>
            <span>Notifications</span>
        </a>

        <!-- 2. Study -->
        <a href="{{ route('student.study-materials') }}" class="mobile-nav-item {{ request()->routeIs('student.study-materials*') ? 'active' : '' }}">
            <i class="fa-solid fa-book-open"></i>
            <span>Study</span>
        </a>

        <!-- 3. Home (Elevated) -->
        <a href="{{ route('student.dashboard') }}" class="mobile-nav-item center-action {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <div class="home-btn">
                <i class="fa-solid fa-house"></i>
            </div>
            <span>Home</span>
        </a>

        <!-- 4. Payments -->
        <a href="{{ route('student.fees') }}" class="mobile-nav-item {{ request()->routeIs('student.fees*') ? 'active' : '' }}">
            <i class="fa-solid fa-credit-card"></i>
            <span>Payments</span>
        </a>

        <!-- 5. Profile -->
        <a href="{{ route('student.profile') }}" class="mobile-nav-item {{ request()->routeIs('student.profile*') ? 'active' : '' }}">
            <i class="fa-solid fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
