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
            <h1 class="text-black">Admins</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> Admin</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="info-box">
              @include('layouts.success')
              <div class="d-flex p-2">

                {{-- <h4 class="text-black">Admins</h4> --}}
                <a href="{{ route('admin.create') }}" class="btn btn-primary ml-auto"> <i class="fa fa-lg fa-plus"></i> Add</a>
              </div>
                {{-- <p>Export data to Copy, CSV, Excel, PDF & Print</p> --}}
                <div class="table-responsive">
                    <table id="example2" class="table table-bordered  table-striped table-hover" data-name="cool-table">
                        <thead>
                            <tr>
                                {{-- <th>ID</th> --}}
                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)

                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    {{-- <td><span class="label label-success">Approved</span></td> --}}
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                        <a href="{{ route('admin.edit', $user->id) }}">
                                            <i class="fa fa-lg fa-pencil ml-2"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="deleteData" data-source="admin" data-id="{{ $user->id }}" data-title="Delete Confirmation?" data-description="This action will delete user named {{ $user->name }}">
                                            <i class="fa fa-lg fa-trash ml-2"></i>
                                        </a>
                                        {{-- <a href="#"
                                            onclick="event.preventDefault(); document.getElementById('delete-form-{{ $user->id }}').submit();">
                                            <i class="fa fa-lg fa-trash ml-2"></i>
                                        </a> --}}

                                        {{-- FOR DELETE --}}
                                        {{-- <form action="{{ route('admin.destroy', $user->id) }}" method="POST"
                                            id="delete-form-{{ $user->id }}" style="display: none;">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <input type="hidden" value="{{ $user->id }}" name="id">
                                        </form> --}}
                                        
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
