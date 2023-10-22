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
                <li><i class="fa fa-angle-right"></i> Payment</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a href="#basic-rounded-tab1" class="nav-link rounded-top active" data-toggle="tab">Payment</a></li>
                        
                    </ul>

                    @include('layouts.errors')
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="basic-rounded-tab1">
                            <form action="{{ route('payment.update', $paypal_transaction_detail->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Payment To</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" readonly
                                            value="{{ $paypal_transaction_detail->advisor->name ?? 'User Not Found' }}">
                                    </div>
                                </div>
    
                                
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Comments</label>
                                    <div class="col-lg-9">
                                        <textarea type="text" class="form-control" required placeholder="Add Comments" name="comments"></textarea>
                                    </div>
                                </div>

                              
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Attachment</label>
                                    <div class="col-lg-9">
                                        <input type="file" class="form-input-styled" data-fouc="" name="image" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf">
                                        <span class="form-text text-muted">Accepted formats: docx, xlsx, pdf and img(png, jpg etc). Max file size 25Mb</span>

                                        {{-- <textarea type="text" class="form-control" required placeholder="Add Comments" name="comments"></textarea> --}}
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary legitRipple">Paid <i
                                            class="icon-paperplane ml-2"></i></button>
                                </div>

                            </form>
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