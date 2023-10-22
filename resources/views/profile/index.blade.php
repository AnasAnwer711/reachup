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
            <h1 class="text-black">My Profile</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> Profile</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            @include('layouts.success')
            @include('layouts.errors')
            @include('layouts.error')
            <div class="row">

            
                <div class="col-md-6">

                    <div class="card">
                        

                        <div class="card-body">
                            <form action="{{ route('profile.update',auth()->user()->id ) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Name:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="John Smith" name="name" value="{{ auth()->user()->name }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Email:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="john@domain.com" name="email" value="{{ auth()->user()->email }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Username:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="JohnSmith" name="username" value="{{ auth()->user()->username }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Phone:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="+92123456789" name="phone" value="{{ auth()->user()->phone }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Current avatar:</label>
                                    
                                    <div class="image text-center"><img src="{{ isset(auth()->user()->image)  ? asset(auth()->user()->image) :  asset('images/default.jpg') }}" class="img-circle" alt="{{ auth()->user()->username }}" onerror=this.src="{{ asset('images/default.jpg') }}" width="100" height="100" id="img-preview"> </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Your avatar:</label>
                                    <div class="col-lg-9">
                                        <div class="uniform-uploader">
                                            <input type="file" class="form-input-styled" data-fouc="" name="image"  onchange="readURL(this);">
                                            {{-- <span class="filename" style="user-select: none;">No file selected</span><span class="action btn bg-pink-400 legitRipple" style="user-select: none;">Choose File</span> --}}
                                        </div>
                                        <span class="form-text text-muted">Accepted formats: gif, png, jpg. Max file size 2Mb</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary legitRipple">Update Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">

                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h5 class="card-title">Change Password</h5>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('update_password', auth()->user()->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Old Password:</label>
                                    <div class="col-lg-9">
                                        <input type="password" class="form-control" placeholder="Enter your old password"
                                            name="old_password">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">New Password:</label>
                                    <div class="col-lg-9">
                                        <input type="password" class="form-control" placeholder="Set your new password"
                                            name="new_password">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Confirm Password:</label>
                                    <div class="col-lg-9">
                                        <input type="password" class="form-control" placeholder="Enter password again"
                                            name="new_password_confirmation">
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary legitRipple">Change Password </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- </div> --}}
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- /.content-wrapper -->

@endsection


@section('javascript')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#img-preview')
                        .attr('src', e.target.result)
                        .width(100)
                        .height(100);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
