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
            <h1 class="text-black">Resolve Report</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i><a
                        href="{{ route('admin_notification.index') }}">Notifications</a> </li>
                <li><i class="fa fa-angle-right"></i> View Details</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a href="#basic-rounded-tab1" class="nav-link rounded-top active" data-toggle="tab">Resolve</a></li>
                        
                    </ul>

                    @include('layouts.errors')
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="basic-rounded-tab1">

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Reported By</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" readonly
                                        value="{{ $admin_notification->source_user->name ?? 'User Not Found' }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Reported To</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" readonly
                                        value="{{ $admin_notification->target_user->name ?? 'User Not Found' }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Message:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control"  readonly
                                        value="{{ $admin_notification->message }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Appropriate Action</label>
                                <div class="col-lg-9">
                                    {{-- <input type="text" class="form-control" readonly
                                        value="{{ $admin_notification->resolve_by }}"> --}}
                                    <select class="form-control" tabindex="-1" aria-hidden="true" name="resolve_by" disabled>
                                        <option value="">Select appropriate action to resolve</option>
                                        <option value="do_nothing" @if($admin_notification->resolve_by == 'do_nothing') selected @endif>Do Nothing</option>
                                        <option value="block"  @if($admin_notification->resolve_by == 'block') selected @endif>Block User</option>
                                        <option value="delete_permanently"  @if($admin_notification->resolve_by == 'delete_permanently') selected @endif>Delete Permanently</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Resolve By</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" readonly
                                        value="{{ $admin_notification->action_user->name ?? 'User Not Found' }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Comments</label>
                                <div class="col-lg-9">
                                    <textarea type="text" class="form-control" placeholder="Add Comments" name="comments" readonly>{{ $admin_notification->comments }}</textarea>
                                </div>
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