<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Mechanix Logo" class="logo-img">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('rentals') ? 'active' : '' }}" href="{{ route('rentals') }}">RENTALS</a>
                </li>
                <li class="nav-item">
                    <span class="nav-separator">|</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('coming') ? 'active' : '' }}" href="{{ route('coming') }}">MEMBERSHIP</a>
                </li>
                <li class="nav-item ms-3">
                    <a class="btn btn-book-now " href="{{ route('booking') }}">Book Now</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="header-redline"></div>
