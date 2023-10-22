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
            <h1 class="text-black">Abuse Users</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> Abuse Users</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="info-box">
              @include('layouts.success')
              @include('layouts.error')
              <div class="d-flex p-2">

                {{-- <h4 class="text-black">Admins</h4> --}}
                {{-- <a href="{{ route('admin_notification.create') }}" class="btn btn-primary ml-auto"> <i class="fa fa-lg fa-plus"></i> Add</a> --}}
              </div>
                {{-- <p>Export data to Copy, CSV, Excel, PDF & Print</p> --}}
                <div class="table-responsive">
                    <table id="example2" class="table table-bordered  table-striped table-hover" data-name="cool-table">
                        <thead>
                            <tr>
                                {{-- <th>ID</th> --}}
                                <th>Message</th>
                                <th>Reported By</th>
                                <th>Reported To</th>
                                <th>Resolve By</th>
                                <th>Reported Time</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($admin_notifications as $admin_notification)

                                <tr>
                                    <td>{{ $admin_notification->message }}</td> 
                                    <td>{{ $admin_notification->source_user->name ?? ($admin_notification->source_name ? $admin_notification->source_name : 'User Deleted') }}</td>
                                    <td>{{ $admin_notification->target_user->name ?? ($admin_notification->target_name ? $admin_notification->target_name : 'User Deleted') }}</td>
                                    <td>{{ $admin_notification->action_user->name ?? ($admin_notification->action_by_name ? $admin_notification->action_by_name : 'User Deleted') }}</td>
                                    <td>{{ $admin_notification->created_at ? date('Y-m-d H:i:s', strtotime($admin_notification->created_at)) : 'Timestamp missing' }}</td>
                                    {{-- <td><span class="label label-success">Approved</span></td> --}}
                                    <td>
                                        @if($admin_notification->resolved)
                                        <span class="badge badge-success">Resolved</span>
                                        <a href="{{ route('admin_notification.show', $admin_notification->id) }}" class="badge badge-primary" style="color: white">Details</a>
                                        @else
                                        <a href="{{ route('admin_notification.edit', $admin_notification->id) }}" class="btn btn-warning">
                                           Resolve
                                        </a>
                                        @endif
                                        
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