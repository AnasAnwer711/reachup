@extends('layouts.app')

@section('css')

    <style>
.toggle.btn{
    min-width: 7.7rem !important;
    min-height: 2.50rem !important;
}

    </style>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header sty-one">
            <h1 class="text-black">Update User</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i><a
                        href="{{ route('allUsers') }}">User</a> </li>
                <li><i class="fa fa-angle-right"></i> Edit</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <ul class="nav nav-tabs">
                        {{-- <li class="nav-item"><a href="#basic-rounded-tab1" class="nav-link rounded-top active" data-toggle="tab">View</a></li>
                        @if(count($user->ratings) > 0)
                        <li class="nav-item"><a href="#basic-rounded-tab2" class="nav-link rounded-top" data-toggle="tab">Ratings <span class="badge badge-default badge-pill ml-2">{{ count($user->ratings) }}</span></a></li>
                        @endif
                        @if(count($user->follows) > 0)
                        <li class="nav-item"><a href="#basic-rounded-tab3" class="nav-link rounded-top" data-toggle="tab">Followers <span class="badge badge-default badge-pill ml-2">{{ count($user->follows) }}</span></a></li>
                        @endif
                        @if(count($user->followers) > 0)
                        <li class="nav-item"><a href="#basic-rounded-tab4" class="nav-link rounded-top" data-toggle="tab">Following <span class="badge badge-default badge-pill ml-2">{{ count($user->followers) }}</span></a></li>
                        @endif --}}
                        <li class="nav-item"><a href="#basic-rounded-tab5" class="nav-link rounded-top active" data-toggle="tab">Change Status</a></li>

                        {{-- @if($category->have_subcategories == 'Yes')
                        <li class="nav-item"><a href="#basic-rounded-tab2" class="nav-link rounded-top" data-toggle="tab">Sub Categories</a></li>
                        @endif --}}
                        
                    </ul>

                    @include('layouts.errors')
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        {{-- <div class="tab-pane fade active show" id="basic-rounded-tab1">

                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Name:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" placeholder="John Smith" name="name" value="{{ $user->name }}" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Email:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" placeholder="john@domain.com" name="email" value="{{ $user->email }}" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Username:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" placeholder="JohnSmith" name="username" value="{{ $user->username }}" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Phone:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" placeholder="+92123456789" name="phone" value="{{ $user->phone }}" disabled>
                                </div>
                            </div>
    
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Current avatar:</label>
                                
                                <div class="image text-center"><img src="{{ $user->image }}" class="img-circle" alt="User Image" height="100" width="100"> </div>
                            </div>


                        </div> --}}
                        {{-- <div class="tab-pane fade" id="basic-rounded-tab2">
                            <div class="d-flex row">
                                @foreach ($user->ratings as $rating)    
                                <div class="card card-body text-center col-2 m-4" style="height: 150px; border-radius:30px;">
                                    <div class="mb-3">
                                        <h5 class="font-weight-semibold mb-0 mt-1">
                                        </h5>

                                            <span class="d-block">{{ $rating->source->name }}</span>
                                            <span class="d-block"><strong>Review:</strong> {{ $rating->reviews }}</span>
                                            @foreach(range(1,5) as $i)
                                                <span class="fa-stack" style="width:1em">
                                                    <i class="fa fa-star-o fa-stack-1x" style="color: orange"></i>

                                                    @if($rating->rate >0)
                                                        @if($rating->rate >0.5)
                                                            <i class="fa fa-star fa-stack-1x" style="color: orange"></i>
                                                        @else
                                                            <i class="fa fa-star-half-o fa-stack-1x" style="color: orange"></i>
                                                        @endif
                                                    @endif
                                                    @php $rating->rate--; @endphp
                                                </span>
                                            @endforeach
                                        
                                    </div>
        
                                </div>
                                @endforeach
                                
                            </div>  
                        </div>   --}}

                        {{-- <div class="tab-pane fade" id="basic-rounded-tab3">
                            <div class="d-flex row">
                                @foreach ($user->follows as $follows)    
                                <div class="card card-body text-center col-2 m-4" style="height: 150px; border-radius:30px;">
                                    <div class="mb-3">
                                        <h5 class="font-weight-semibold mb-0 mt-1">

                                        </h5>
                                        <span class="d-block">{{ $follows->follower_user->name }}</span>
                                        <span class="d-block">{{ $follows->follower_user->email }}</span>
                                        <span class="d-block">{{ $follows->follower_user->phone }}</span>

                                        <span class="d-block">
                                            @php
                                                $rating = $follows->follower_user->avg_rating
                                            @endphp
                                            @foreach(range(1,5) as $i)
                                                <span class="fa-stack" style="width:1em">
                                                    <i class="fa fa-star-o fa-stack-1x" style="color: orange"></i>
                                                    
                                                    @if($rating >0)
                                                        @if($rating >0.5)
                                                            <i class="fa fa-star fa-stack-1x" style="color: orange"></i>
                                                        @else
                                                            <i class="fa fa-star-half-o fa-stack-1x" style="color: orange"></i>
                                                        @endif
                                                    @endif
                                                    @php $rating--; @endphp
                                                </span>
                                            @endforeach    
                                        </span>
                                    </div>
        
                                </div>
                                @endforeach
                                
                            </div>  
                        </div>   --}}

                        {{-- <div class="tab-pane fade" id="basic-rounded-tab4">
                            <div class="d-flex row">
                                @foreach ($user->followers as $follower)    
                                <div class="card card-body text-center col-2 m-4" style="height: 150px; border-radius:30px;">
                                    <div class="mb-3">
                                        <h5 class="font-weight-semibold mb-0 mt-1">

                                        </h5>
                                        <span class="d-block">{{ $follower->following_user->name }}</span>
                                        <span class="d-block">{{ $follower->following_user->email }}</span>
                                        <span class="d-block">{{ $follower->following_user->phone }}</span>

                                        <span class="d-block">
                                            @php
                                                $rating = $follower->following_user->avg_rating
                                            @endphp
                                            @foreach(range(1,5) as $i)
                                                <span class="fa-stack" style="width:1em">
                                                    <i class="fa fa-star-o fa-stack-1x" style="color: orange"></i>
                                                    
                                                    @if($rating >0)
                                                        @if($rating >0.5)
                                                            <i class="fa fa-star fa-stack-1x" style="color: orange"></i>
                                                        @else
                                                            <i class="fa fa-star-half-o fa-stack-1x" style="color: orange"></i>
                                                        @endif
                                                    @endif
                                                    @php $rating--; @endphp
                                                </span>
                                            @endforeach    
                                        </span>
                                    </div>
        
                                </div>
                                @endforeach
                                
                            </div>  
                        </div>   --}}

                        <div class="tab-pane fade active show" id="basic-rounded-tab5">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Name:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" value="{{ ucfirst($user->name) }}" disabled>
                                </div>
                                
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Email:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" value="{{ ucfirst($user->email) }}" disabled>
                                </div>
                                
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Username:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" value="{{ ucfirst($user->username) }}" disabled>
                                </div>
                                
                            </div>
                            <form action="{{ route('update_user_status', $user->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Current Status:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="+92123456789" name="phone" value="{{ ucfirst($user->status) }}" disabled>
                                    </div>
                                    
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Change Status:</label>
                                    <div class="col-lg-9">                                    
                                        <input type="checkbox" name="status" @if($user->status == 'active')checked @endif data-toggle="toggle" data-on="Active" data-off="Blocked" data-onstyle="success" data-offstyle="danger">
                                    </div>
                                    
                                </div>
                                <div class="text-right">
                                    <a href="{{ route('allUsers') }}" class="btn btn-primary legitRipple">Cancel <i
                                        class="icon-paperplane ml-2"></i></a>
                                <button type="submit" class="btn btn-primary legitRipple">Update <i
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
