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
            <h1 class="text-black">Reportings</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> <a href="{{ route('reporting.index') }}">Reporting</a></li>
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
                    <form action="{{ route('reporting.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Title:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="Reporting/Sub Reporting Title" name="title">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Parent reporting (Optional)</label>
                            <div class="col-lg-9">
                            <select class="form-control" tabindex="-1" aria-hidden="true" name="parent_id">
                                <option value="">Select parent reporting to make sub reporting</option>
                                @foreach ($parent_reportings as $parent_reporting)
                                    <option value="{{ $parent_reporting->id }}">{{ $parent_reporting->title }}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>

                        <div class="text-right">
                            <a href="{{ route('reporting.index') }}" class="btn btn-primary legitRipple">Cancel </a>
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
