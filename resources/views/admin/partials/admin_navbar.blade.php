<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="{{ route('admin.home') }}" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <img src="{{ asset('assets/admin/images/logo-dark.png') }}" class="img-fluid logo-lg" alt="logo">
      </a>
    </div>
    <div class="navbar-content">
      <ul class="pc-navbar">
        <li class="pc-item">
          <a href="{{ route('admin.home') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>

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

     

     
      </ul>
      
    </div>
  </div>
</nav>