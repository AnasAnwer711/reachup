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
            <h1 class="text-black">Coupon</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> <a href="{{ route('coupon.index') }}">Coupon</a></li>
                <li><i class="fa fa-angle-right"></i> Add</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Add New</h5>
                    @include('layouts.errors')
                    @include('layouts.error')
                </div>

                <div class="card-body">
                    <form action="{{ route('coupon.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Code:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="Code" name="code">
                                <input type="hidden" name="is_active" value="1">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Start:</label>
                            <div class="col-lg-9">
                                <input type="datetime-local" id="start" name="start" class="form-control">
                                {{-- <input type="text" class="form-control" placeholder="Code" name="title"> --}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">End:</label>
                            <div class="col-lg-9">
                                <input type="datetime-local" id="end" name="end" class="form-control">
                                {{-- <input type="text" class="form-control" placeholder="Code" name="title"> --}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Percentage:</label>
                            <div class="col-lg-9">
                                <input type="number" onkeydown="if(event.key==='.'){event.preventDefault();}" required min="1" max="100" class="form-control" placeholder="Percentage" name="percentage">
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <a href="{{ route('coupon.index') }}" class="btn btn-primary legitRipple">Cancel </a>
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
        // $("#start").val(new Date().toJSON().slice(0,19));
        // $("#end").val(new Date().toJSON().slice(0,19));
    </script>
@endsection
