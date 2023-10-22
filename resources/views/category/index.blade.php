@extends('layouts.app')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet"
integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
{{-- <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous"> --}}
<link href="{{ asset('dist/css/bstreeview.min.css') }}" rel="stylesheet">

@section('css')

    <style>


    </style>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header sty-one">
            <h1 class="text-black">Categories</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> Category</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="info-box">
              @include('layouts.success')
              @include('layouts.error')
              <div class="d-flex p-2">

                {{-- <h4 class="text-black">Admins</h4> --}}
                <a href="{{ route('category.create') }}" class="btn btn-primary ml-auto"> <i class="fa fa-lg fa-plus"></i> Add</a>
              </div>

              <!-- Main content -->
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a href="#basic-rounded-tab1" class="nav-link rounded-top active" data-toggle="tab">Tree View</a></li>
                            <li class="nav-item"><a href="#basic-rounded-tab2" class="nav-link rounded-top" data-toggle="tab">List View</a></li>
                            
                        </ul>

                        @include('layouts.errors')
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="basic-rounded-tab1">
                                <div id="tree"></div>
                            </div>
                            <div class="tab-pane fade" id="basic-rounded-tab2">
                            <div class="table-responsive">
                                    <table id="example2" class="table table-bordered  table-striped table-hover" data-name="cool-table">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Title</th>
                                                <th>Parent Category</th>
                                                <th>Associated Users</th>
                                                <th>Revenue Genrated</th>
                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categories as $category)

                                                <tr>
                                                    <td><img class="" src="{{ $category->image }}" alt="category_image" width="100px" height="100px"> </td>
                                                    <td>{{ $category->title }}</td>
                                                    <td>{{ $category->parent_category->title ?? '' }}</td>
                                                    <td>{{ $category->category_interests ?? 0 }}</td>
                                                    <td>${{ number_format($category->revenue_generated ?? 0, 2) }}</td>
                                                    <td>
                                                        <a href="{{ route('category.edit', $category->id) }}">
                                                            <i class="fa fa-lg fa-pencil ml-2"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" class="deleteData" data-source="category" data-id="{{ $category->id }}" data-title="Delete Confirmation!" data-description="This action will delete your {{ $category->title }} category">
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
                    </div>
                </div>
            
            </div>
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection


@section('javascript')
<script src="{{ asset('dist/js/bstreeview.min.js') }}"></script>

    <script>
		
        $( document ).ready(function() {
            var json = [];
            $.ajax({
                type: 'GET', //THIS NEEDS TO BE GET
                url: 'get_categories',
                success: function (data) {

                    $.each(data,function(index,value){
                        var nodes = [];

                        for(var i = 0; i < value.sub_categories.length; i++) {
                            // obj.push(value.sub_categories[i]);
                            nodes.push({
                                // icon: "fa fa-inbox fa-fw",
                                text: value.sub_categories[i].title
                            },);

                        }
                            // console.log(nodes);
                        // $("#days").append('<input type="checkbox" value="'+value+'" name="days[]"  > '+value+'<br/>');
                        var title = '<span class="m-2">'+value.title+'</span><a target="_self" href="category/'+value.id+'/edit"><i class="fa fa-pencil m-2"></i></a>';
                        if(value.have_subcategories){
                            json.push(
                                {
                                    text: title,
                                    icon: "fa fa-list fa-fw",
                                    nodes: nodes
                                },
                            );
                        } else {
                            json.push(
                                {
                                    text: title,
                                    icon: "fa fa-list fa-fw",
                                },
                            );
                        }

                    });
                },
                error: function() { 
                    // console.log(data);
                }
            });
            setTimeout(() => {
                
                // console.log(json);
                $('#tree').bstreeview({
                    data: json,
                    expandIcon: 'fa fa-angle-down fa-fw',
                    collapseIcon: 'fa fa-angle-right fa-fw',
                    indent: 1.25,
                    parentsMarginLeft: '1.25rem',
                    openNodeLinkOnNewTab: true
                });
            }, 1000);
			

            

		});

        $('#tree').on('click','a',function(){
            window.location=$(this).attr('href');
        });
    </script>
@endsection
