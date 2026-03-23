<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Mechanix Logo" class="logo-img">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end mobile-sidebar" id="navbarNav">

            <!-- Sidebar Header with Close Button (Mobile Only) -->
            <div class="sidebar-header d-lg-none">
                <div class="mobile-welcome">
                    Welcome Guest
                </div>
                <button class="btn-close btn-close-white" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"></button>
            </div>

            <ul class="navbar-nav">

                <!-- Home (Mobile Only) -->
                <li class="nav-item mobile-only">
                    <a class="nav-link mobile-menu-item" href="{{ route('home') }}">
                        <div class="menu-item-content">
                            <i class="bi bi-house-door menu-icon"></i>
                            <span>Home</span>
                        </div>
                    </a>
                </li>

                <!-- Desktop Main Links -->
                <li class="nav-item desktop-only">
                    <a class="nav-link {{ request()->routeIs('rentals') ? 'active' : '' }}"
                        href="{{ route('rentals') }}">
                        RENTALS
                    </a>
                </li>

                <li class="nav-item desktop-only">
                    <a class="nav-link {{ request()->routeIs('coming') ? 'active' : '' }}" href="{{ route('coming') }}">
                        MEMBERSHIP
                    </a>
                </li>

                <li class="nav-item desktop-only">
                    <a class="btn btn-book-now w-100 mt-2" href="{{ route('booking') }}">
                        Book Now
                    </a>
                </li>

                <!-- Rentals Dropdown (Mobile Only) -->
                <li class="nav-item mobile-only">
                    <div class="mobile-dropdown">
                        <button class="mobile-dropdown-toggle" type="button" data-bs-toggle="collapse"
                            data-bs-target="#rentalsDropdown">
                            <div class="menu-item-content">
                                <i class="bi bi-tools menu-icon"></i>
                                <span>Rentals</span>
                            </div>
                            <i class="bi bi-chevron-down dropdown-arrow"></i>
                        </button>
                        <div class="collapse mobile-dropdown-menu" id="rentalsDropdown">
                            <a href="{{ route('coming') }}" class="dropdown-sub-item">Lift Rentals & Tool Rentals</a>
                            <a href="{{ route('coming') }}" class="dropdown-sub-item">Speciality Tools</a>
                            <a href="{{ route('coming') }}" class="dropdown-sub-item">Technician On Site</a>
                            <a href="{{ route('coming') }}" class="dropdown-sub-item">AC Service</a>
                            <a href="{{ route('coming') }}" class="dropdown-sub-item">Alignment</a>
                        </div>
                    </div>
                </li>

                <!-- Membership (Mobile Only) -->
                <li class="nav-item mobile-only">
                    <a class="nav-link mobile-menu-item" href="{{ route('coming') }}">
                        <div class="menu-item-content">
                            <i class="bi bi-award menu-icon"></i>
                            <span> Membership</span>
                        </div>
                    </a>
                </li>

                <!-- More Dropdown (Mobile Only) -->
                <li class="nav-item mobile-only">
                    <div class="mobile-dropdown">
                        <button class="mobile-dropdown-toggle" type="button" data-bs-toggle="collapse"
                            data-bs-target="#moreDropdown">
                            <div class="menu-item-content">
                                <i class="bi bi-grid menu-icon"></i>
                                <span>More</span>
                            </div>
                            <i class="bi bi-chevron-down dropdown-arrow"></i>
                        </button>
                        <div class="collapse mobile-dropdown-menu" id="moreDropdown">
                            <a href="#" class="dropdown-sub-item">
                                <i class="bi bi-telephone sub-icon"></i>
                                Contact Us
                            </a>
                            <a href="#" class="dropdown-sub-item">
                                <i class="bi bi-shield-check sub-icon"></i>
                                Privacy Policy
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Account Section (Mobile Only) -->
                @guest
                    <li class="nav-item mobile-only">
                        <a class="nav-link mobile-menu-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#mxAuthModal" id="openLogin">
                            <div class="menu-item-content">
                                <i class="bi bi-box-arrow-in-right menu-icon"></i>
                                <span>Login</span>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item mobile-only">
                        <a class="nav-link mobile-menu-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#mxAuthModal" id="openRegister">
                            <div class="menu-item-content">
                                <i class="bi bi-person-plus menu-icon"></i>
                                <span>Sign Up</span>
                            </div>
                        </a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item mobile-only">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="mobile-logout-btn">
                                <i class="bi bi-box-arrow-right menu-icon"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                @endauth

                <!-- AUTH DROPDOWN (Desktop Only) -->
                <li class="nav-item dropdown desktop-only">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="authDropdown"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-4"></i>
                        <span class="ms-2">Account</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        @guest
                            <li>
                                <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                    data-bs-target="#mxAuthModal" id="openLogin">
                                    Login
                                </a>
                            </li>
                            <li>
                                <a href="#" class="dropdown-item" id="openRegister" data-bs-toggle="modal"
                                    data-bs-target="#mxAuthModal">
                                    Sign Up
                                </a>
                            </li>
                        @endguest

                        @auth
                            <li>
                                <span class="dropdown-item-text">
                                    Hello {{ strtok(auth()->user()->email, '@') }}
                                </span>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a href="{{ route('admin.home') }}" class="dropdown-item">
                                    Admin Dashboard
                                </a>
                            </li><br>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="text-center">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        Logout
                                    </button>
                                </form>
                            </li>
                        @endauth
                    </ul>
                </li>

            </ul>
        </div>

    </div>
</nav>

<div class="header-redline"></div>

<script>
    // Close mobile sidebar when clicking on links
    document.addEventListener('DOMContentLoaded', function() {
        const navbarCollapse = document.getElementById('navbarNav');
        const mobileLinks = document.querySelectorAll(
        '.mobile-menu-item, .dropdown-sub-item, .mobile-book-btn');

        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                        toggle: false
                    });
                    bsCollapse.hide();
                }
            });
        });

        // Handle modal triggers - open register tab when clicking Sign Up
        const registerMobileTrigger = document.getElementById('openRegisterMobile');
        const registerDesktopTrigger = document.getElementById('openRegister');

        if (registerMobileTrigger) {
            registerMobileTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                // Switch to register tab
                const registerTab = document.querySelector(
                '#mxAuthModal .nav-tabs a[href="#register"]');
                if (registerTab) {
                    const tab = new bootstrap.Tab(registerTab);
                    tab.show();
                }
                // Close sidebar
                const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                    toggle: false
                });
                bsCollapse.hide();
            });
        }

        if (registerDesktopTrigger) {
            registerDesktopTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                // Switch to register tab
                const registerTab = document.querySelector(
                '#mxAuthModal .nav-tabs a[href="#register"]');
                if (registerTab) {
                    const tab = new bootstrap.Tab(registerTab);
                    tab.show();
                }
            });
        }

        // Handle login triggers - ensure login tab is shown
        const loginMobileTrigger = document.getElementById('openLoginMobile');
        const loginDesktopTrigger = document.getElementById('openLogin');

        if (loginMobileTrigger) {
            loginMobileTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                // Switch to login tab
                const loginTab = document.querySelector('#mxAuthModal .nav-tabs a[href="#login"]');
                if (loginTab) {
                    const tab = new bootstrap.Tab(loginTab);
                    tab.show();
                }
                // Close sidebar
                const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                    toggle: false
                });
                bsCollapse.hide();
            });
        }

        if (loginDesktopTrigger) {
            loginDesktopTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                // Switch to login tab
                const loginTab = document.querySelector('#mxAuthModal .nav-tabs a[href="#login"]');
                if (loginTab) {
                    const tab = new bootstrap.Tab(loginTab);
                    tab.show();
                }
            });
        }

        // Rotate arrow on dropdown toggle
        const dropdownToggles = document.querySelectorAll('.mobile-dropdown-toggle');
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const arrow = this.querySelector('.dropdown-arrow');
                arrow.classList.toggle('rotated');
            });
        });
    });
</script>
