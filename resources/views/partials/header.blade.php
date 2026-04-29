<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Mechanix Logo" class="logo-img">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end mobile-sidebar" id="navbarNav">

            <!-- Sidebar Header -->
            <div class="sidebar-header d-lg-none">
                <div class="mobile-welcome">Welcome Guest</div>
                <button class="btn-close btn-close-white" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"></button>
            </div>

            <ul class="navbar-nav">

                <!-- Mobile Home -->
                <li class="nav-item mobile-only">
                    <a class="nav-link mobile-menu-item" href="{{ route('home') }}">
                        <div class="menu-item-content">
                            <i class="bi bi-house-door menu-icon"></i>
                            <span>Home</span>
                        </div>
                    </a>
                </li>

                <!-- Desktop -->
                <li class="nav-item desktop-only">
                    <a class="nav-link {{ request()->routeIs('rentals') ? 'active' : '' }}"
                        href="{{ route('rentals') }}">
                        RENTALS
                    </a>
                </li>

                <li class="nav-item desktop-only">
                    <a class="nav-link {{ request()->routeIs('membership') ? 'active' : '' }}"
                        href="{{ route('membership') }}">
                        MEMBERSHIP
                    </a>
                </li>

                <li class="nav-item desktop-only">
                    <a class="btn btn-book-now w-100 mt-2" href="{{ route('booking') }}">
                        Book Now
                    </a>
                </li>

                <!-- Mobile Rentals -->
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
                            <a href="{{ route('coming') }}" class="dropdown-sub-item">Lift Rentals</a>
                            <a href="{{ route('coming') }}" class="dropdown-sub-item">Speciality Tools</a>
                            <a href="{{ route('coming') }}" class="dropdown-sub-item">Technician On Site</a>
                        </div>
                    </div>
                </li>

                <!-- Mobile Membership -->
                <li class="nav-item mobile-only">
                    <a class="nav-link mobile-menu-item" href="{{ route('membership') }}">
                        <div class="menu-item-content">
                            <i class="bi bi-award menu-icon"></i>
                            <span>Membership</span>
                        </div>
                    </a>
                </li>

                <!-- Mobile More -->
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
                            <a href="{{ route('contact') }}" class="dropdown-sub-item">Contact Us</a>
                            <a href="{{ route('privacy') }}" class="dropdown-sub-item">Privacy Policy</a>
                        </div>
                    </div>
                </li>

                <!-- AUTH MOBILE -->
                @guest
                <li class="nav-item mobile-only">
                    <a class="nav-link mobile-menu-item" href="#" data-bs-toggle="modal"
                        data-bs-target="#mxAuthModal" id="openLoginMobile">
                        <div class="menu-item-content">
                            <i class="bi bi-box-arrow-in-right menu-icon"></i>
                            <span>Login</span>
                        </div>
                    </a>
                </li>

                <li class="nav-item mobile-only">
                    <a class="nav-link mobile-menu-item" href="#" data-bs-toggle="modal"
                        data-bs-target="#mxAuthModal" id="openRegisterMobile">
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

                <!-- DESKTOP ACCOUNT -->
                <li class="nav-item dropdown desktop-only">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
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
                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                data-bs-target="#mxAuthModal" id="openRegister">
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
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
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
document.addEventListener('DOMContentLoaded', function () {
    const navbarCollapse = document.getElementById('navbarNav');

    document.querySelectorAll('.mobile-menu-item, .dropdown-sub-item').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                new bootstrap.Collapse(navbarCollapse, { toggle: false }).hide();
            }
        });
    });

    ['openRegisterMobile','openRegister'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('click', e => {
                e.preventDefault();
                const tab = document.querySelector('#mxAuthModal a[href="#register"]');
                if (tab) new bootstrap.Tab(tab).show();
            });
        }
    });

    ['openLoginMobile','openLogin'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('click', e => {
                e.preventDefault();
                const tab = document.querySelector('#mxAuthModal a[href="#login"]');
                if (tab) new bootstrap.Tab(tab).show();
            });
        }
    });

    document.querySelectorAll('.mobile-dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function () {
            const arrow = this.querySelector('.dropdown-arrow');
            if (arrow) arrow.classList.toggle('rotated');
        });
    });
});
</script>