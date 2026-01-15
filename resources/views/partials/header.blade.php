<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Mechanix Logo" class="logo-img">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>



        <div class="collapse navbar-collapse justify-content-end mobile-sidebar" id="navbarNav">
            <div class="sidebar-close d-lg-none">
                <button class="btn-close btn-close-white"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav">
                </button>
            </div>
            <ul class="navbar-nav">

                <!-- Main Links -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('rentals') ? 'active' : '' }}" href="{{ route('rentals') }}">
                        RENTALS
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('coming') ? 'active' : '' }}" href="{{ route('coming') }}">
                        MEMBERSHIP
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-book-now w-100 mt-2" href="{{ route('booking') }}">
                        Book Now
                    </a>
                </li>
                <!-- SERVICES TITLE (MOBILE ONLY) -->
                <li class="nav-item mobile-only mt-4 mb-2">
                    <span class="text-white fw-bold">SERVICES</span>
                </li>

                <!-- SERVICES LINKS (MOBILE ONLY) -->
                <li class="nav-item mobile-only">
                    <a class="nav-link mobile-service-link" href="{{ route('coming') }}">Lift Rentals & Tool Rentals</a>
                </li>

                <li class="nav-item mobile-only">
                    <a class="nav-link mobile-service-link" href="{{ route('coming') }}">Speciality Tools</a>
                </li>

                <li class="nav-item mobile-only">
                    <a class="nav-link mobile-service-link" href="{{ route('coming') }}">Technician On Site</a>
                </li>

                <li class="nav-item mobile-only">
                    <a class="nav-link mobile-service-link" href="{{ route('coming') }}">AC Service</a>
                </li>

                <li class="nav-item mobile-only">
                    <a class="nav-link mobile-service-link" href="{{ route('coming') }}">Alignment</a>
                </li>

                <li class="mobile-footer-header mobile-only">More</li>
                <li class="mobile-only"><a href="#">Contact Us</a></li>
                <li class="mobile-only"><a href="#">Privacy Policy</a></li>



            </ul>
        </div>

    </div>
</nav>

<div class="header-redline"></div>