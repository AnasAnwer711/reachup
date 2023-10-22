@extends('layouts.app')

@section('css')

    <style>


    </style>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header sty-one">
            <h1 class="text-black">Add Admin</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> <a href="{{ route('admin.index') }}">Admin</a></li>
                <li><i class="fa fa-angle-right"></i> Add</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Add New</h5>
                    @include('layouts.errors')
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Name:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="John Smith" name="name" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Email:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="john@domain.com" name="email" value="{{ old('email') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Username:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="JohnSmith" name="username" value="{{ old('username') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Phone:</label>
                            <div class="col-lg-9">
                                <input type="hidden" name="selected_country" value="pk" id="selected_country">
                                <input aria-invalid="true" required type="tel" name="phone"  value="{{ old('phone') }}" id="phone_int" class="form-control phone_int">
                                {{-- <input type="text" class="form-control" placeholder="65123456789 OR 1234567890" onkeypress="return isNumberKey(event)" name="phone" maxlength="11"> --}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Password:</label>
                            <div class="col-lg-9">
                                <input type="password" class="form-control" placeholder="Enter your password"
                                    name="password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Confirm Password:</label>
                            <div class="col-lg-9">
                                <input type="password" class="form-control" placeholder="Enter password again"
                                    name="password_confirmation">
                            </div>
                        </div>

                        {{-- <div class="form-group row">
            <label class="col-lg-3 col-form-label">Your state:</label>
            <div class="col-lg-9">
              <select class="form-control" tabindex="-1" aria-hidden="true">
                <option value="1" disabled>User</option>
                <option value="2" disabled>Professional</option>
                <option value="3" selected>Admin</option>
              </select>
            </div>
          </div> --}}


                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Your avatar:</label>
                            <div class="col-lg-9">
                                <div class="uniform-uploader">
                                    <input type="file" class="form-input-styled" data-fouc="" name="image">
                                    {{-- <span class="filename" style="user-select: none;">No file selected</span><span class="action btn bg-pink-400 legitRipple" style="user-select: none;">Choose File</span> --}}
                                </div>
                                <span class="form-text text-muted">Accepted formats: gif, png, jpg. Max file size 2Mb</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('admin.index') }}" class="btn btn-primary legitRipple">Cancel </a>
                            <button type="submit" class="btn btn-primary legitRipple">Save </button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- </div> --}}
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
@endsection


@section('javascript')
    <script>

    </script>
@endsection
