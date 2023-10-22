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
            <h1 class="text-black">Report/Spam Menu</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> Report/Spam Menu</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="info-box">
              @include('layouts.success')
              @include('layouts.error')
              <div class="d-flex p-2">

                {{-- <h4 class="text-black">Admins</h4> --}}
                <a href="{{ route('reporting.create') }}" class="btn btn-primary ml-auto"> <i class="fa fa-lg fa-plus"></i> Add</a>
              </div>
                {{-- <p>Export data to Copy, CSV, Excel, PDF & Print</p> --}}
                <div class="table-responsive">
                    <table id="example2" class="table table-bordered  table-striped table-hover" data-name="cool-table">
                        <thead>
                            <tr>
                                {{-- <th>ID</th> --}}
                                <th>Title</th>
                                <th>Parent Report</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportings as $report)

                                <tr>
                                    <td>{{ $report->title }}</td>
                                    <td>{{ $report->parent_reporting->title ?? '' }}</td>
                                    <td>
                                        <a href="{{ route('reporting.edit', $report->id) }}">
                                            <i class="fa fa-lg fa-pencil ml-2"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="deleteData" data-source="reporting" data-id="{{ $report->id }}" data-title="Delete Confirmation!" data-description="This action will delete your {{ $report->title }} reporting">
                                            <i class="fa fa-lg fa-trash ml-2"></i>
                                        </a>
                                        
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection


@section('javascript')
    <script>

    </script>
@endsection
