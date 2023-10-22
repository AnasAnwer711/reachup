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
            <h1 class="text-black">Professional</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> Professional</li>
                {{-- <li><i class="fa fa-angle-right"></i> Data Tables</li> --}}
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">

            <div class="info-box">
                {{-- <h4 class="text-black">List of Professional</h4> --}}
                {{-- <p>Export data to Copy, CSV, Excel, PDF & Print</p> --}}
                <div class="table-responsive">
                    <table id="example5" class="table table-bordered  table-striped table-hover report" data-name="Professional">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Average Rating</th>
                                <th>Total Earning</th>
                                <th>Total Followers</th>
                                <th>Total Following</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($professionals as $professional)

                                <tr>
                                    <td>{{ $professional->name }}</td>
                                    <td>{{ $professional->email }}</td>
                                    <td>{{ $professional->username }}</td>
                                    <td>{{ $professional->phone }}</td>
                                    <td><span
                                            class="label label-{{ $professional->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($professional->status) }}</span>
                                    </td>
                                    <td>{{ number_format($professional->avg_rating, 1) }}</td>
                                    <td>${{ number_format($professional->total_earning, 2) }}</td>
                                    <td>{{ number_format($professional->my_total_followers) }}</td>
                                    <td>{{ number_format($professional->my_total_followings) }}</td>
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
