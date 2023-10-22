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
            <h1 class="text-black">Users</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> Users</li>
                {{-- <li><i class="fa fa-angle-right"></i> Data Tables</li> --}}
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">

            <div class="info-box">
                {{-- <h4 class="text-black">List of User</h4> --}}
                {{-- <p>Export data to Copy, CSV, Excel, PDF & Print</p> --}}
                <div class="table-responsive">
                    <table id="example3" class="table table-bordered  table-striped table-hover report" data-name="Users">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Average Rating</th>
                                <th>Total Spent</th>
                                <th>Total Followers</th>
                                <th>Total Following</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)

                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td><span
                                            class="label label-{{ $user->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($user->status) }}</span>
                                    </td>
                                    <td>{{ number_format($user->avg_rating, 1) }}</td>
                                    <td>${{ number_format($user->total_spent, 2) }}</td>
                                    <td>{{ number_format($user->my_total_followers) }}</td>
                                    <td>{{ number_format($user->my_total_followings) }}</td>
                                    
                                    
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
