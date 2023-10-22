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
                <li class="sub-bread"><i class="fa fa-angle-right"></i><a
                        href="{{ route('reporting.index') }}">Reporting</a> </li>
                <li><i class="fa fa-angle-right"></i> Edit</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a href="#basic-rounded-tab1" class="nav-link rounded-top active" data-toggle="tab">Edit</a></li>
                        @if(count($reporting->sub_reportings) > 0)
                        <li class="nav-item"><a href="#basic-rounded-tab2" class="nav-link rounded-top" data-toggle="tab">Sub Reportings</a></li>
                        @endif
                        
                    </ul>

                    @include('layouts.errors')
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="basic-rounded-tab1">
                            <form action="{{ route('reporting.update', $reporting->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Title:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="John Smith" name="title"
                                            value="{{ $reporting->title }}">
                                    </div>
                                </div>
                                @if(count($reporting->sub_reportings) < 1)
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Parent Reporting (Optional)</label>
                                    <div class="col-lg-9">
                                    <select class="form-control" tabindex="-1" aria-hidden="true" name="parent_id">
                                        <option value="">Select parent reporting to make sub reporting</option>
                                        @foreach ($parent_reportings as $parent_reporting)
                                            <option value="{{ $parent_reporting->id }}" @if($parent_reporting->id == $reporting->parent_id) selected @endif>{{ $parent_reporting->title }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                                @endif
                                <div class="text-right">
                                    <a href="{{ route('reporting.index') }}" class="btn btn-primary legitRipple">Cancel <i
                                        class="icon-paperplane ml-2"></i></a>
                                <button type="submit" class="btn btn-primary legitRipple">Update <i
                                        class="icon-paperplane ml-2"></i></button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="basic-rounded-tab2">
                            <div class="d-flex row">
                                @foreach ($reporting->sub_reportings as $sub_reporting)    
                                <div class="card card-body text-center col-2 m-4" style="height: 150px">
                                    <div class="mb-3">
                                        <h5 class="font-weight-semibold mb-0 mt-1">
                                            {{ $sub_reporting->title }}
                                        </h5>
                                        {{-- <span class="d-block">Head of UX</span> --}}
                                    </div>
        
                                    <ul class="list-inline mb-0">
                                        {{-- <li class="list-inline-item"><a href="#" class="btn btn-outline btn-icon text-white btn-lg border-white rounded-round legitRipple">
                                            <i class="icon-phone"></i></a>
                                        </li> --}}
                                        <li class="list-inline-item"><a href="javascript:void(0)"  class="btn btn-outline-dark btn-icon text-red btn-lg border-black rounded-round deleteData" data-source="reporting" data-id="{{ $sub_reporting->id }}" data-title="Delete Confirmation!" data-description="This action will delete your {{ $sub_reporting->title }} reporting" >
                                            <i class="fa fa-trash mr-2"></i> Remove</a>
                                        </li>
                                    </ul>
                                </div>
                                @endforeach
                                
                            </div>  
                        </div>  
                    </div>
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
