<header class="main-header"> 
  <!-- Logo --> 
  <a href="{{ route('dashboard') }}" class="logo blue-bg"> 
  <!-- mini logo for sidebar mini 50x50 pixels --> 
  <span class="logo-mini"><img src=" {{ asset('dist/img/logo-n.png') }}" alt=""></span> 
  <!-- logo for regular state and mobile devices --> 
  <span class="logo-lg"><img src=" {{ asset('dist/img/logo.png') }}" alt=""></span> </a> 
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar blue-bg navbar-static-top"> 
    <!-- Sidebar toggle button-->
    <ul class="nav navbar-nav pull-left">
      <li><a class="sidebar-toggle" data-toggle="push-menu" href=""></a> </li>
    </ul>
    {{-- <div class="pull-left search-box">
      <form action="#" method="get" class="search-form">
        <div class="input-group">
          <input name="search" class="form-control" placeholder="Search..." type="text">
          <span class="input-group-btn">
          <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i> </button>
          </span></div>
      </form>
      <!-- search form --> 
    </div> --}}
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        
        <!-- Notifications: style can be found in dropdown.less -->
        <li class="dropdown messages-menu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="position: relative"> <i class="fa fa-bell-o"></i>
          <div class="notify" style="position: absolute; top: 14px; right: 10px"> <span class="badge old_count"></span> </div>
          </a>
          <ul class="dropdown-menu">
            <li class="header">Notifications</li>
            <li>
              <input type="hidden" name="old_count" id="old_count" value="0">
              <ul class="menu" id="not-container" style="max-height: 250px">
                {{-- <li><a href="#"> --}}
                
              </ul>
            </li>
            {{-- <li class="footer"><a href="{{ route('admin_notification.index') }}">Check all Notifications</a></li> --}}
          </ul>
        </li>
        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu p-ph-res"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <img src=" {{ isset(auth()->user()->image)  ? asset(auth()->user()->image) :  asset('images/default.jpg') }}" class="user-image" alt="{{ auth()->user()->username }}"  onerror=this.src="{{ asset('images/default.jpg') }}"> <span class="hidden-xs">{{ auth()->user()->name }}</span> </a>
          <ul class="dropdown-menu">
            <li class="user-header">
              <div class="pull-left user-img"><img src=" {{ isset(auth()->user()->image)  ? asset(auth()->user()->image) :  asset('images/default.jpg') }}" class="img-responsive" alt="{{ auth()->user()->username }}"  onerror=this.src="{{ asset('images/default.jpg') }}"></div>
              <p class="text-left">{{ auth()->user()->name }} <small>{{ auth()->user()->username }}</small> </p>
              <div class="view-link text-left"><a href="{{ route('profile.index') }}">View Profile</a> </div>
            </li>
            {{-- <li><a href="#"><i class="icon-profile-male"></i> My Profile</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#"><i class="icon-gears"></i> Account Setting</a></li>
            <li role="separator" class="divider"></li> --}}
            <li><a href="{{ route('logout') }}"><i class="fa fa-power-off"></i> Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>
