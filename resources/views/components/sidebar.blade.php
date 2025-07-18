<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">Admin</a>
        </div>
        <br>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">A</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="menu-dashboard {{ Request::is('home') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('home') }}"><i class="fa fa-home"></i>
                    <span>Dashboard</span></a>
            </li>

            <li class="menu-header">Menu Utama</li>
            <li class="menu-data-cuti {{ Request::is('cuti*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('cuti.index') }}"><i class="fa fa-users"></i> <span>Data
                        Cuti</span></a>
            </li>

            <li class="menu-ruangan {{ Request::is('ruangan*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('ruangan.index') }}"><i class="fa fa-tags"></i>
                    <span>Ruangan/Bagian</span></a>
            </li>

            <li class="menu-karyawan {{ Request::is('karyawan*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('karyawan.index') }}"><i class="fa fa-user"></i>
                    <span>Data Karyawan</span></a>
            </li>

            <li class="menu-notifikasi {{ Request::is('notifications*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('notifications.index') }}"><i class="fa fa-bell"></i>
                    <span>Notifikasi</span></a>
            </li>
        </ul>
        <div class="hide-sidebar-mini mt-4 mb-4 p-3">
            <a href="#" class="btn btn-danger btn-lg btn-block btn-icon-split"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
            <form id='logout-form' method="POST" action="{{ route('logout') }}">
                @csrf
            </form>
        </div>
    </aside>
</div>
