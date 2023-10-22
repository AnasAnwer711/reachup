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
            <h1 class="text-black">Reachups</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> Reachups</li>
                {{-- <li><i class="fa fa-angle-right"></i> Data Tables</li> --}}
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">

            <div class="info-box">
                {{-- <h4 class="text-black">List of Reachups</h4> --}}
                {{-- <p>Export data to Copy, CSV, Excel, PDF & Print</p> --}}
                <div class="table-responsive">
                    <table id="example4" class="table table-bordered  table-striped table-hover report" data-name="Reachups">
                        <thead>
                            <tr>
                                <th>Request By </th>
                                <th>Request To</th>
                                <th>Status</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Duration</th>
                                <th>Starting Date</th>
                                <th>Ending Date</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reachups as $reachup)
                                <tr>
                                    <td>{{ $reachup->user->name ?? '' }}</td>
                                    <td>{{ $reachup->advisor->name ?? '' }}</td>
                                    <td>
                                        <span class="label label-@if ($reachup->status ==
                                        'pending')warning @elseif($reachup->status
                                        =='process')primary @elseif($reachup->status ==
                                        'completed')success @elseif($reachup->status ==
                                            'reject')danger @endif">
                                            {{ ucfirst($reachup->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $reachup->category->title ?? '' }}</td>
                                    <td>${{ number_format($reachup->charges,2) }}</td>
                                    <td>{{ $reachup->session_duration }}</td>
                                    <td>{{ $reachup->date }} {{ $reachup->from_time }}</td>
                                    <td>{{ $reachup->date }} {{ $reachup->to_time }}</td>

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
