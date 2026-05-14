<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="{{ route('admin.dashboard') }}" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <img src="{{ asset('template/admin/dist/assets/images/logo-dark.svg') }}" class="img-fluid logo-lg" alt="logo">
        <span class="badge bg-light-success rounded-pill ms-2 theme-version">v2.6.0</span>
      </a>
    </div>
    <div class="navbar-content">
      <div class="card pc-user-card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <img src="{{ asset('template/admin/dist/assets/images/user/avatar-1.jpg') }}" alt="user-image"
                class="user-avtar wid-45 rounded-circle" />
            </div>
            <div class="flex-grow-1 ms-3 me-2">
              <h6 class="mb-0">Jonh Smith</h6>
              <small>Administrator</small>
            </div>
            <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse" href="#pc_sidebar_userlink">
              <svg class="pc-icon">
                <use xlink:href="#custom-sort-outline"></use>
              </svg>
            </a>
          </div>
          <div class="collapse pc-user-links" id="pc_sidebar_userlink">
            <div class="pt-3">
              <a href="#!">
                <i class="ti ti-user"></i>
                <span>My Account</span>
              </a>
              <a href="#!">
                <i class="ti ti-settings"></i>
                <span>Settings</span>
              </a>
              <a href="#!">
                <i class="ti ti-lock"></i>
                <span>Lock Screen</span>
              </a>
              <a href="#!">
                <i class="ti ti-power"></i>
                <span>Logout</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <ul class="pc-navbar">
        <li class="pc-item pc-caption">
          <label>Navigation</label>
        </li>

        <li class="pc-item">
          <a href="{{ route('admin.dashboard') }}" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-status-up"></use>
              </svg>
            </span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>

        <li class="pc-item pc-caption">
          <label>Akademik</label>
          <svg class="pc-icon">
            <use xlink:href="#custom-presentation-chart"></use>
          </svg>
        </li>
        <li class="pc-item">
          <a href="{{ route('admin.akademik.tahun-pelajaran.index') }}" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-calendar-1"></use>
              </svg>
            </span>
            <span class="pc-mtext">Tahun Pelajaran</span>
          </a>
        </li>

        <li class="pc-item pc-caption">
          <label>Setting</label>
          <svg class="pc-icon">
            <use xlink:href="#custom-setting-2"></use>
          </svg>
        </li>
        <li class="pc-item">
          <a href="{{ route('admin.settings.school.index') }}" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-status-up"></use>
              </svg>
            </span>
            <span class="pc-mtext">Sekolah</span>
          </a>
        </li>
        <li class="pc-item pc-hasmenu {{ Request::routeIs('admin.settings.rbac.*') ? 'active pc-trigger' : '' }}">
          <a href="#!" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-setting-2"></use>
              </svg>
            </span>
            <span class="pc-mtext">Sistem</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu">
            <li class="pc-item {{ Request::routeIs('admin.settings.rbac.index') ? 'active' : '' }}">
              <a class="pc-link" href="{{ route('admin.settings.rbac.index') }}">RBAC</a>
            </li>
            <li class="pc-item {{ Request::routeIs('admin.settings.user.index') ? 'active' : '' }}">
              <a class="pc-link" href="{{ route('admin.settings.user.index') }}">Pengguna</a>
            </li>
          </ul>
        </li>

        <li class="pc-item pc-caption">
          <label>Other</label>
          <svg class="pc-icon">
            <use xlink:href="#custom-notification-status"></use>
          </svg>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-level"></use>
              </svg> </span><span class="pc-mtext">Menu levels</span><span class="pc-arrow"><i
                data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Level 2.1</a></li>
            <li class="pc-item pc-hasmenu">
              <a href="#!" class="pc-link">Level 2.2<span class="pc-arrow"><i
                    data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu">
                <li class="pc-item"><a class="pc-link" href="#!">Level 3.1</a></li>
                <li class="pc-item"><a class="pc-link" href="#!">Level 3.2</a></li>
                <li class="pc-item pc-hasmenu">
                  <a href="#!" class="pc-link">Level 3.3<span class="pc-arrow"><i
                        data-feather="chevron-right"></i></span></a>
                  <ul class="pc-submenu">
                    <li class="pc-item"><a class="pc-link" href="#!">Level 4.1</a></li>
                    <li class="pc-item"><a class="pc-link" href="#!">Level 4.2</a></li>
                  </ul>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        <li class="pc-item"><a href="#!" class="pc-link">
            <span class="pc-micon">
              <svg class="pc-icon">
                <use xlink:href="#custom-notification-status"></use>
              </svg>
            </span>
            <span class="pc-mtext">Sample page</span></a>
        </li>

      </ul>
    </div>
  </div>
</nav>
<!-- [ Sidebar Menu ] end -->