<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar"> 
  <!-- sidebar: style can be found in sidebar.less -->
  <div class="sidebar"> 
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="image text-center"><img src="{{ isset(auth()->user()->image)  ? asset(auth()->user()->image) :  asset('images/default.jpg') }}" class="img-circle" alt="{{ auth()->user()->username }}" onerror=this.src="{{ asset('images/default.jpg') }}"> </div>
      <div class="info">
        <p>{{ auth()->user()->name }}</p>
        <a href="{{ route('logout') }}"><i class="fa fa-power-off"></i></a> </div>
    </div>
    
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">PERSONAL </li>
      <li class=""> <a href="{{ route('dashboard') }}"> <i class="fa fa-dashboard"></i> <span>Dashboard</span> </a></li>
      @if(auth()->check() && auth()->user()->is_superadmin)
      <li class=""> <a href="{{ route('admin.index') }}"> <i class="fa fa-users"></i> <span>Admins</span> </a></li>
      @endif
      <li class=""> <a href="{{ route('category.index') }}"> <i class="fa fa-list"></i> <span>Categories</span> </a></li>
      <li class=""> <a href="{{ route('allUsers') }}"> <i class="fa fa-users"></i> <span>Users</span> </a></li>
      {{-- @if(auth()->check() && auth()->user()->is_superadmin) --}}
      <li class=""> <a href="{{ route('admin_notification.index') }}"> <i class="fa fa-user-times"></i> <span>Abuse Users</span> </a></li>
      {{-- @endif --}}
      <li class=""> <a href="{{ route('reporting.index') }}"> <i class="fa fa-ban"></i> <span>Report/Spam Menu</span> </a></li>
      {{-- <li class=""> <a href="{{ route('coupon.index') }}"> <i class="fa fa-percent"></i> <span>Coupon</span> </a></li> --}}
      {{-- <li class=""> <a href="{{ route('report') }}"> <i class="fa fa-file"></i> <span>Reports</span> </a></li> --}}
      <li class="treeview @if(Request::path() == 'users' || Request::path() == 'professionals' || Request::path() == 'reachups') menu-open @endif"> 
        {{-- <a href="{{ route('report') }}"> <i class="fa fa-file"></i> <span>Reports</span> </a> --}}
        <a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-file"></i>
          <span class="app-menu__label">Reachup Details</span>
          <i class="treeview-indicator fa float-right mt-2 @if(Request::path() == 'users' || Request::path() == 'professionals' || Request::path() == 'reachups') fa-chevron-down @else fa-chevron-right @endif"></i>
        </a>
        
        <ul class="treeview-menu" style="display:@if(Request::path() == 'users' || Request::path() == 'professionals' || Request::path() == 'reachups') block @else none @endif">
            <li>
                <a class="treeview-item d-flex justify-content-between" href="{{ route('users') }}" >
                    <span class="text-white"><i class="app-menu__icon fa fa-users"></i> Users</span>
                    {{-- <span class="badge badge-primary mr-2">4</span> --}}
                </a>
                <a class="treeview-item d-flex justify-content-between" href="{{ route('professionals') }}" >
                  <span class="text-white"><i class="app-menu__icon fa fa-user-secret"></i> Professionals</span>
                </a>
                <a class="treeview-item d-flex justify-content-between" href="{{ route('reachups') }}" >
                  <span class="text-white"><i class="app-menu__icon ti-stats-up"></i> Reachups</span>
                </a>
            </li>
        </ul>
      
      </li>
      @if(auth()->check() && auth()->user()->is_superadmin)
      <li class=""> <a href="{{ route('setting.create') }}"> <i class="fa fa-cogs"></i> <span>Settings</span> </a></li>
      @endif
      @if(auth()->check() && auth()->user()->is_superadmin)
      <li class=""> <a href="{{ route('payment.index') }}"> <i class="fa fa-money"></i> <span>Payments</span> </a></li>
      @endif
      {{-- <li class=""> <a href="{{ route('payment') }}"> <i class="fa fa-money"></i> <span>Payment List</span> </a></li> --}}
    </ul>
  </div>
  <!-- /.sidebar --> 
</aside>