@extends('layouts.app')

@section('css')

    <style>
        .check-parent, .check-child{
            opacity: 0.5;

        }
        .btn-primary.active, .btn-primary:active, .show>.btn-primary.dropdown-toggle {
            color: #fff;
            background-color: #eb740e;
            background-image: none;
            border-color: #000;
            opacity: 1;
        }

    </style>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header sty-one">
            <h1 class="text-black">Update Category</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i><a
                        href="{{ route('category.index') }}">Category</a> </li>
                <li><i class="fa fa-angle-right"></i> Edit</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a href="#basic-rounded-tab1" class="nav-link rounded-top active" data-toggle="tab">Edit</a></li>
                        @if($category->have_subcategories)
                        <li class="nav-item"><a href="#basic-rounded-tab2" class="nav-link rounded-top" data-toggle="tab">Sub Categories</a></li>
                        @endif
                        
                    </ul>

                    @include('layouts.errors')
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="basic-rounded-tab1">
                            <div class="form-group row d-flex">
                                <button class="btn btn-primary btn-lg m-2 check-parent @if(!$category->parent_id) active @endif">Parent Category</button>
                                <button class="btn btn-primary btn-lg m-2 check-child @if($category->parent_id) active @endif">Child Category</button>
                            </div>
                            <form action="{{ route('category.update', $category->id) }}" method="POST"
                                enctype="multipart/form-data" id="myForm">
                                @csrf
                                @method('PUT')
                                <div style="display: none;">
                                    <label><input type="radio" name="category" id="parent" value="parent" @if(!$category->parent_id) checked @endif> Parent</label>
                                    <label><input type="radio" name="category" id="child" value="child" @if($category->parent_id) checked @endif> Child</label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Title:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="John Smith" name="title"
                                            value="{{ $category->title }}">
                                    </div>
                                </div>
                                @if(!$category->have_subcategories)
                                <div class="form-group row" id="child-div" style="display:  @if(!$category->parent_id) none @endif;">
                                    <label class="col-lg-3 col-form-label">Parent Category</label>
                                    <div class="col-lg-9">
                                    <select class="form-control" tabindex="-1" aria-hidden="true" name="parent_id" id="parent_category">
                                        <option value="">Select parent category to make sub category</option>
                                        @foreach ($parent_categories as $parent_category)
                                            <option value="{{ $parent_category->id }}" @if($parent_category->id == $category->parent_id) selected @endif>{{ $parent_category->title }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                                @endif

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Current Image:</label>
                                    <div class="image text-center"><img src="{{ $category->image }}" class="img-circle"
                                            alt="User Image" height="100" width="100"> </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Change Image:</label>
                                    <div class="col-lg-9">
                                        <div class="uniform-uploader">
                                            <input type="file" class="form-input-styled" name="image">
                                            {{-- <span class="filename" style="user-select: none;">No file selected</span><span class="action btn bg-pink-400 legitRipple" style="user-select: none;">Choose File</span> --}}
                                        </div>
                                        <span class="form-text text-muted">Accepted formats: gif, png, jpg. Max file size 2Mb</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Keywords:</label>
                                    <div class="col-lg-9">
                                        <input type="text" value="{{ $category_keywords }}" class="inputKeywords" name="keywords" id="keyword" data-role="tagsinput" tabindex="-1"  />
                                        {{-- <div class="field_wrapper">
                                            <div class="mb-2">
                                                <input type="text" name="field_name[]" value="" id="keyword"/>
                                                <a href="javascript:void(0);" class="add_button" title="Add field"><i class="fa fa-plus ml-2"></i></a>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <a href="{{ route('category.index') }}" class="btn btn-primary legitRipple">Cancel <i
                                            class="icon-paperplane ml-2"></i></a>
                                    <button type="submit" class="btn btn-primary legitRipple">Update <i
                                            class="icon-paperplane ml-2"></i></button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="basic-rounded-tab2">
                            <div class="d-flex row">

                                @foreach ($category->sub_categories as $sub_category)    
                                <div class="card card-body text-center col-2 m-4" >
                                    <div class="mb-3">
                                        <h5 class="font-weight-semibold mb-0 mt-1">
                                            {{ $sub_category->title }}
                                        </h5>
                                        {{-- <span class="d-block">Head of UX</span> --}}
                                    </div>
        
                                    <a href="#" class="d-inline-block mb-3">
                                        <img src="{{ $sub_category->image }}" class="rounded-round img-circle" width="110" height="110" alt="">
                                    </a>
        
                                    <ul class="list-inline mb-0" >
                                        {{-- <li class="list-inline-item"><a href="#" class="btn btn-outline btn-icon text-white btn-lg border-white rounded-round legitRipple">
                                            <i class="icon-phone"></i></a>
                                        </li> --}}
                                        <li class="list-inline-item"><a href="javascript:void(0)"  class="btn btn-outline-dark btn-icon text-red btn-lg border-black rounded-round deleteData" data-source="category" data-id="{{ $sub_category->id }}" data-title="Delete Confirmation!" data-description="This action will delete your {{ $sub_category->title }} category" >
                                            <i class="fa fa-trash mr-2"></i> Remove</a>
                                        </li>
                                    </ul>
                                </div>
                                @endforeach
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
        $(".check-parent").click(function(e){
            e.preventDefault();
            $(this).addClass( "active" );
            $(this).css( "opacity", 1 );
            $(".check-child").removeClass( "active" );
            $(".check-child").css( "opacity", 0.5 );
            $("#child-div").css( "display", "none" );
            $('#parent_category').attr('required', false)
            $("#parent").prop("checked", true);

        });
        $(".check-child").click(function(e){
            e.preventDefault();
            $(this).addClass( "active" );
            $(this).css( "opacity", 1 );

            $(".check-parent").removeClass( "active" );
            $(".check-parent").css( "opacity", 0.5 );
            $("#child-div").css( "display", "flex" );
            $('#parent_category').attr('required', true)
            $("#child").prop("checked", true);

        });
    </script>
@endsection
