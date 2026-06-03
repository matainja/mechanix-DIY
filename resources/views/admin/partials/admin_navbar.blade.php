<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('admin.home') }}" class="b-brand text-primary">
                <img src="{{ asset('assets/admin/images/logo-dark.png') }}" class="img-fluid logo-lg" alt="logo">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">

                {{-- ✅ Visible to ALL roles --}}
                <li class="pc-item">
                    <a href="{{ route('admin.home') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                {{-- ✅ Management section: Superadmin + Subadmin only (role == 1) --}}
                @if(auth()->user()->role == 1)
                <li class="pc-item pc-caption">
                    <label>Management</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                <li class="pc-item">
                    <a href="{{ route('admin.bookings') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-shopping-cart-discount"></i></span>
                        <span class="pc-mtext">Bookings</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="{{ route('admin.holidays.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-calendar-event"></i></span>
                        <span class="pc-mtext">Holidays</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="{{ route('admin.users') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-users"></i></span>
                        <span class="pc-mtext">Manage Users</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="{{ route('admin.admin.membership.requests') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-crown"></i></span>
                        <span class="pc-mtext">Membership</span>
                    </a>
                </li>
                @endif

                {{-- ✅ Products & Pricing: Superadmin ONLY (role == 1 + email match) --}}
                @if(auth()->user()->role == 1 && auth()->user()->email == config('admin.superadmin_email'))
                <li class="pc-item">
                    <a href="{{ route('admin.products.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-package"></i></span>
                        <span class="pc-mtext">Products & Pricing</span>
                    </a>
                </li>
                @endif

                {{-- ✅ Profile & Settings: ALL roles --}}
                <li class="pc-item pc-caption">
                    <label>Account</label>
                    <i class="ti ti-user fs-4"></i>
                </li>
                <li class="pc-item">
                    <a href="{{ route('user.profile') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-user-circle"></i></span>
                        <span class="pc-mtext">Profile</span>
                    </a>
                </li>
                {{-- <li class="pc-item">
                    <a href="{{ route('admin.settings') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-settings"></i></span>
                        <span class="pc-mtext">Settings</span>
                    </a>
                </li> --}}

            </ul>
        </div>
    </div>
</nav>