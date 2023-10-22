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
            <h1 class="text-black">Update Admin</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i><a href="{{ route('admin.index') }}">Admin</a> </li>
                <li><i class="fa fa-angle-right"></i> Edit</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Edit</h5>
                    @include('layouts.errors')
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Name:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="John Smith" name="name" value="{{ $user->name }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Email:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="john@domain.com" name="email" value="{{ $user->email }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Username:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="JohnSmith" name="username" value="{{ $user->username }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Phone:</label>
                            <div class="col-lg-9">
                                <input type="hidden" name="selected_country" value="{{ $user->phone_code }}" id="selected_country">
                                <input aria-invalid="true" required type="tel" name="phone"  value="{{ $user->phone }}" id="phone_int" class="form-control phone_int">
                                
                                {{-- <input type="text" class="form-control" placeholder="+92123456789" name="phone" value="{{ $user->phone }}" maxlength="13"> --}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Current avatar:</label>
                            
                            <div class="image text-center"><img src="{{ $user->image }}" class="img-circle" alt="User Image" height="100" width="100"> </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Change avatar:</label>
                            <div class="col-lg-9">
                                <div class="uniform-uploader">
                                    <input type="file" class="form-input-styled" data-fouc="" name="image">
                                    {{-- <span class="filename" style="user-select: none;">No file selected</span><span class="action btn bg-pink-400 legitRipple" style="user-select: none;">Choose File</span> --}}
                                </div>
                                <span class="form-text text-muted">Accepted formats: gif, png, jpg. Max file size 2Mb</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('admin.index') }}" class="btn btn-primary legitRipple">Cancel <i
                                class="icon-paperplane ml-2"></i></a>
                        <button type="submit" class="btn btn-primary legitRipple">Update <i
                                class="icon-paperplane ml-2"></i></button>
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
        var iso = $('#selected_country').val();
        var phone = $('#phone_int').val();
        console.log(iso);
        console.log(phone);
        setTimeout(() => {
            iti.setCountry((iso).toString());
            
            iti.setNumber((phone).toString());
        }, 500);
    </script>
@endsection
