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
            <h1 class="text-black">Coupons</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> Coupon</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="info-box">
              @include('layouts.success')
              @include('layouts.error')
              <div class="d-flex p-2">

                {{-- <h4 class="text-black">Admins</h4> --}}
                <a href="{{ route('coupon.create') }}" class="btn btn-primary ml-auto"> <i class="fa fa-lg fa-plus"></i> Add</a>
              </div>
                {{-- <p>Export data to Copy, CSV, Excel, PDF & Print</p> --}}
                <div class="table-responsive">
                    <table id="example2" class="table table-bordered  table-striped table-hover" data-name="cool-table">
                        <thead>
                            <tr>
                                {{-- <th>ID</th> --}}
                                <th>Code</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Percentage</th>
                                <th>Active</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $coupon)

                                <tr>
                                    <td>{{ $coupon->code }}</td>
                                    <td>{{ $coupon->start }}</td>
                                    <td>{{ $coupon->end }}</td>
                                    <td>{{ $coupon->percentage }}</td>
                                    <td>{{ $coupon->is_active }}</td>
                                    <td>
                                        <a href="{{ route('coupon.edit', $coupon->id) }}">
                                            <i class="fa fa-lg fa-pencil ml-2"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="deleteData" data-source="coupon" data-id="{{ $coupon->id }}" data-title="Delete Confirmation!" data-description="This action will delete your {{ $coupon->title }} coupon">
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
