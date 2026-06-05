<style>
    .pc-header,
    .header-wrapper {
        overflow: visible !important;
    }

    .dropdown-menu {
        z-index: 9999 !important;
        left:80px

    }
    /* .drp-search {
    left: 80px !important;
} */
</style>
<header class="pc-header">
    <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="dropdown pc-h-item d-inline-flex d-md-none">
                    <a class="pc-head-link dropdown-toggle arrow-none m-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-search"></i>
                    </a>
                    <div class="dropdown-menu pc-h-dropdown drp-search">
                        <form class="px-3">
                            <div class="form-group mb-0 d-flex align-items-center">
                                <i data-feather="search"></i>
                                <input type="search" class="form-control border-0 shadow-none"
                                    placeholder="Search here. . .">
                            </div>
                        </form>
                    </div>
                </li>
                <li class="pc-h-item d-none d-md-inline-flex">
                    <form class="header-search">
                        <i data-feather="search" class="icon-search"></i>
                        <input type="search" class="form-control" placeholder="Search here. . .">
                    </form>
                </li>
            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled d-flex align-items-center mb-0">

                <!-- MOBILE PROFILE DROPDOWN (Only visible on small screens) -->
@auth
   <li class="dropdown pc-h-item d-md-none" style="position: relative;">
        <a href="#" class="pc-head-link dropdown-toggle arrow-none m-0"
    id="mobileProfileDropdown"
    data-bs-toggle="dropdown"
    aria-expanded="false"
    style="padding: 0 8px; overflow: visible;">
    <div style="width:36px; height:36px; border-radius:50%; background:#5b6ef5; 
                display:flex; align-items:center; justify-content:center; 
                font-weight:600; color:white; font-size:0.85rem; line-height:1;
                border: 2px solid rgba(255,255,255,0.2);
                flex-shrink: 0;">
        {{ strtoupper(substr(auth()->user()->email, 0, 1)) }}
    </div>
</a>

        <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2"
            aria-labelledby="mobileProfileDropdown">
            <li>
                <span class="dropdown-item-text fw-semibold">
                    Hello {{ strtok(auth()->user()->email, '@') }}
                </span>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a href="{{ route('home') }}" class="dropdown-item">
                    <i class="bi bi-speedometer2 me-2"></i> Front end
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </li>
@endauth
                <!-- DESKTOP PROFILE DROPDOWN -->
                <li class="dropdown pc-h-item d-none d-md-block">
                    <a href="#" class="pc-head-link dropdown-toggle arrow-none" id="profileDropdown"
    data-bs-toggle="dropdown" aria-expanded="false">
    <div style="width:36px; height:36px; border-radius:50%; background:#5b6ef5; 
                display:flex; align-items:center; justify-content:center; 
                font-weight:600; color:white; font-size:0.85rem; line-height:1;
                border: 2px solid rgba(255,255,255,0.2);">
        {{ strtoupper(substr(auth()->user()->email, 0, 1)) }}
    </div>
</a>

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2">

                        @auth
                            <li>
                                <span class="dropdown-item-text fw-semibold">
                                    Hello {{ strtok(auth()->user()->email, '@') }}
                                </span>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            {{-- <li>
                                <a href="{{ route('admin.home') }}" class="dropdown-item">
                                    <i class="bi bi-speedometer2 me-2"></i> Admin Dashboard
                                </a>
                            </li> --}}
                            {{-- <li>
                                <hr class="dropdown-divider">
                            </li> --}}
                            <li>
                                <a href="{{ route('home') }}" class="dropdown-item">
                                    <i class="bi bi-speedometer2 me-2"></i> Front end
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        @endauth

                        

                    </ul>
                </li>

            </ul>
        </div>
    </div>
</header>
