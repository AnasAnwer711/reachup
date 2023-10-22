@extends('layouts.app')

@section('css')

    <style>
        .btn-primary.active, .btn-primary:active, .show>.btn-primary.dropdown-toggle {
            color: #fff;
            background-color: #eb740e;
            background-image: none;
            border-color: #000;
        }
        .check-child{
            opacity: 0.5;
        }

    </style>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header sty-one">
            <h1 class="text-black">Add Category</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> <a href="{{ route('category.index') }}">Category</a></li>
                <li><i class="fa fa-angle-right"></i> Add</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Add New</h5>
                    @include('layouts.errors')
                    @include('layouts.error')
                </div>
                {{-- #eb740e --}}
                
                <div class="card-body">
                    <div class="form-group row d-flex">
                        <button class="btn btn-primary btn-lg m-2 check-parent active">Parent Category</button>
                        <button class="btn btn-primary btn-lg m-2 check-child">Child Category</button>
                    </div>
                    <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data" id="myForm">
                        @csrf
                        {{-- <div style="display: none;">
                            <label><input type="radio" name="category" id="parent" value="parent" checked> Parent</label>
                            <label><input type="radio" name="category" id="child" value="child"> Child</label>
                        </div> --}}
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Title:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="Category/Sub Category Title" name="title">
                            </div>
                        </div>
                        <div class="form-group row" id="child-div" style="display: none;">
                            <label class="col-lg-3 col-form-label">Parent Category</label>
                            <div class="col-lg-9">
                            <select class="form-control" tabindex="-1" aria-hidden="true" name="parent_id" id="parent_category">
                                <option value="">Select parent category to make sub category</option>
                                @foreach ($parent_categories as $parent_category)
                                    <option value="{{ $parent_category->id }}">{{ $parent_category->title }}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Image:</label>
                            <div class="col-lg-9">
                                <div class="uniform-uploader">
                                    <input type="file" class="form-input-styled" name="image">
                                </div>
                                <span class="form-text text-muted">Accepted formats: gif, png, jpg. Max file size 2Mb</span>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Keywords:</label>
                            <div class="col-lg-9">
                                <input type="text" value="" class="inputKeywords" name="keywords" id="keyword" data-role="tagsinput" tabindex="-1"  />
                                {{-- <div class="field_wrapper">
                                    <div class="mb-2">
                                        <input type="text" name="field_name[]" value="" id="keyword"/>
                                        <a href="javascript:void(0);" class="add_button" title="Add field"><i class="fa fa-plus ml-2"></i></a>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('category.index') }}" class="btn btn-primary legitRipple">Cancel </a>
                            <button type="submit" class="btn btn-primary legitRipple">Save </button>
                        </div>
                    </form>
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
        $(document).ready(function(){
            document.getElementById("myForm").onkeypress = function(e) {
                var key = e.charCode || e.keyCode || 0;     
                if (key == 13) {
                    // alert("I told you not to, why did you do it?");
                    e.preventDefault();
                }
            }
        });
        $('#keyword').tagsinput({
            tagClass: function(item) {
                return 'label label-warning';
            }
        });
        $(".check-parent").click(function(){
            $(this).addClass( "active" );
            $(this).css( "opacity", 1 );
            $(".check-child").removeClass( "active" );
            $(".check-child").css( "opacity", 0.5 );
            $("#child-div").css( "display", "none" );
            // $("#parent").prop("checked", true);
            $('#parent_category').attr('required', false)
        });
        $(".check-child").click(function(){
            $(this).addClass( "active" );
            $(this).css( "opacity", 1 );

            $(".check-parent").removeClass( "active" );
            $(".check-parent").css( "opacity", 0.5 );
            $("#child-div").css( "display", "flex" );
            // $("#child").prop("checked", true);
            $('#parent_category').attr('required', true)


        });
    </script>
@endsection
