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
            <h1 class="text-black">Payment</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> Payment</li>
            </ol>
        </div>


        <!-- Main content -->
        <div class="content">
            <div class="info-box">
                @include('layouts.success')
                @include('layouts.error')
                {{-- <h4 class="text-black">Payment list</h4> --}}
                {{-- <p>Export data to Copy, CSV, Excel, PDF & Print</p> --}}
                <div class="table-responsive">
                    <table id="example2" class="table table-bordered  table-striped table-hover report" data-name="cool-table">
                        <thead>
                            <tr>
                                <th>Payment For</th>
                                <th>Comments</th>
                                <th>Status</th>
                                <th>Amount</th> 
                                <th>Attachment</th> 
                                <th>Action</th> 

                            </tr>
                        </thead>
                        <tbody>
                            
                            
                            @foreach ($paypal_transaction_details as $value)
                            <tr>
                                <td>{{ $value->advisor->name }}</td>
                                <td>{{ $value->comments }}</td>
                                <td><span style="text-transform: capitalize" class="label label-{{ $value->status == 'paid' ? 'success' : 'danger' }}">{{ $value->status }}</span></td>
                                <td>{{ number_format($value->pay_amount, 2) }}</td>
                                <td>@if($value->file) <a href="{{ $value->file }}" target="_blank" class="text-info">Open File</a> @endif</td>
                                <td>
                                    @if($value->status == 'unpaid') 
                                    <a href="{{ route('payment.edit', $value->id) }}">
                                        <i class="fa fa-lg fa-money ml-2 text-danger"></i>
                                    </a>
                                    @else
                                    <i class="fa fa-lg fa-handshake-o ml-2 text-success"></i> 
                                    @endif</td>
                            </tr>
                            @endforeach
                            {{-- <tr>
                                <td>1132</td>
                                <td>Paypal</td>
                                <td><span class="label label-success">Approved</span></td>
                                <td>300$</td>
                                <td>Fahad Mustafa</td>
                                <td>Ghulam Mustafa</td>
                                <td>1/11/2021</td>
                                <td>#847683</td>
                            </tr> --}}
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
