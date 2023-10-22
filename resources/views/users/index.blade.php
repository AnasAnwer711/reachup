@extends('layouts.app')
@section('css')

    <style>
        .cstStyle{
            display: block;
            border: 1px solid;
            color: #000;
            padding: 10px
        }

        a:hover, a:active, a:focus {
            color: #f39c12 !important;
        }

    </style>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header sty-one">
            <h1 class="text-black">Users</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> Users</li>
            </ol>
        </div>

        <!-- Main content -->
        <div class="content">
            <div class="info-box">
                @include('layouts.success')
                @include('layouts.error')
                @include('layouts.errors')
                {{-- <p>Export data to Copy, CSV, Excel, PDF & Print</p> --}}
                <form id="search-form">

                    <div class="row">
                    
                        <div class="form-group col-md-3">
                            <h5>Name <span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Please enter name">
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <h5>Email <span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="text" name="email" id="email" class="form-control"
                                    placeholder="Please enter email">
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <h5>User Type <span class="text-danger"></span></h5>
                            <div class="controls">
                                <select name="user_type" id="user_type" class="form-control">
                                    <option value="">Select User Type</option>
                                    <option value="user">User</option>
                                    <option value="advisor">Advisor</option>
                                </select>
                                {{-- <input type="text" name="user_type" id="user_type" class="form-control"
                                    placeholder="Please enter email"> --}}

                                
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <h5>Status <span class="text-danger"></span></h5>
                            <div class="controls">
                                <select name="status" id="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="block">Blocked</option>
                                </select>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-3">
                            <h5>Following From<span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="number" onkeyup="onlyNumbers(this)" name="following_from" id="following_from" class="form-control"
                                    placeholder="Please enter following greater than">
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <h5>Following To<span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="number" onkeyup="onlyNumbers(this)" name="following_to" id="following_to" class="form-control"
                                    placeholder="Please enter following less than">
                                <div class="help-block"></div>
                            </div>
                        </div>

                        <div class="form-group col-md-3">
                            <h5>Follower From<span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="number" onkeyup="onlyNumbers(this)" name="follower_from" id="follower_from" class="form-control"
                                    placeholder="Please enter follower greater than">
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <h5>Follower To<span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="number" onkeyup="onlyNumbers(this)" name="follower_to" id="follower_to" class="form-control"
                                    placeholder="Please enter follower less than">
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="form-group col-md-3">
                            <h5>Rating From<span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="number" maxlength="3" name="rating_from" id="rating_from" class="form-control"
                                    placeholder="Please enter rating greater than">
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <h5>Rating To<span class="text-danger"></span></h5>
                            <div class="controls">
                                <input type="number" maxlength="3" name="rating_to" id="rating_to" class="form-control"
                                    placeholder="Please enter rating less than">
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-left" style="
                            margin-left: 15px;
                            ">
                        <button type="button" id="btnFiterSubmitSearch" class="btn btn-info">Search</button>

            
                            {{-- <input type="text" name="lead_phone"> --}}
                        <button type="button" class="btn btn-info" data-toggle="modal"
                        data-target="#notifyToMultipleUserModal" >Send Notify to Search Users</button>
                    </div>

                </form>
 
                <br>

                <div class="table-responsive">
                    <table id="user-data-table" class="table table-bordered data-table table-striped table-hover"
                        data-name="cool-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>UserType</th>
                                <th>Status</th>
                                <th>Advisor Status</th>
                                <th>Average Rating</th>
                                <th>Total Spent</th>
                                <th>Total Followers</th>
                                <th>Total Following</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>

                    </table>

                    
 <!-- Action Button Modal -->
 <div class="modal fade" id="viewActionModal" tabindex="-1"
    role="dialog" aria-labelledby="viewActionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewActionModalLabel">
                    Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <fieldset>
                    <div class="col-12">
                        <a href="javascript:void(0)" class="cstStyle action" data-toggle="modal" id="action-notify">
                            <i class="fa fa-bell ml-2"></i>
                            <span class="m-2">
                                Notify
                            </span>
                        </a>
                        <a href="javascript:void(0)" class="cstStyle action" data-toggle="modal" id="action-view">
                            <i class="fa fa-eye ml-2"></i>
                            <span class="m-2">
                                View
                            </span>
                        </a>
                        <a class="cstStyle action" id="action-edit">
                            <i class="fa fa-pencil ml-2"></i>
                            <span class="m-2">
                                Edit
                            </span>
                        </a>
                        <a href="javascript:void(0)" data-toggle="modal" class="cstStyle action" id="action-status" style="display: none">
                            <i class="fa fa-exchange ml-2"></i>
                            <span class="m-2">
                                Status
                            </span>
                        </a>
                        <a class="cstStyle action" id="action-delete">
                            <i class="fa fa-trash ml-2"></i>
                            <span class="m-2">
                                Delete
                            </span>
                        </a>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>


                    <!-- Notify Multiple Search Users Modal -->
        <div class="modal fade notifyToMultipleModal" id="notifyToMultipleUserModal"  tabindex="-1"
            role="dialog" aria-labelledby="notifyToMultipleUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notifyToMultipleUserModalLabel">
                            Notify Search Users</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="mulitple-notify-form">

                            <div class="div_notification" >
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Title:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="Title" name="title">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Message:</label>
                                    <div class="col-lg-9">
                                        {{-- <textarea class="ck_editor" name="post" id="" cols="30" rows="10"></textarea> --}}
                                        <textarea name="message" class="form-control"  placeholder="Notification Message"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Type:</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" tabindex="-1" aria-hidden="true" name="type">
                                            <option value="" disabled selected>Select Notification Type</option>
                                            <option value="payment">Payment</option>
                                            <option value="chat">Chat</option>
                                            <option value="request">Reachup Request</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Image:</label>
                                    <div class="col-lg-9">
                                        <div class="uniform-uploader">
                                            <input type="file" class="form-input-styled" data-fouc="" name="image">
                                            {{-- <span class="filename" style="user-select: none;">No file selected</span><span class="action btn bg-pink-400 legitRipple" style="user-select: none;">Choose File</span> --}}
                                        </div>
                                        <span class="form-text text-muted">Accepted formats: gif, png, jpg. Max file size 2Mb</span>
                                    </div>
                                </div>
                            </div>
                                
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">Close</button>
                                <button type="button" id="submit-multiple-notify"  class="btn btn-primary">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

                  @foreach($users as $user)
                      <!-- Notify Users Modal -->
        <div class="modal fade notifyModal" id="notifyUserModal{{ $user->id }}" data-notifyid="{{ $user->id }}" tabindex="-1"
            role="dialog" aria-labelledby="notifyUserModalLabel{{ $user->id }}"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notifyUserModalLabel{{ $user->id }}" data-notifyid="{{ $user->id }}">
                            Notify User {{ $user->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('notify_user', $user->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                
                                
                                <label class="col-lg-3 col-form-label">Notify from:</label>
                                <div class="col-lg-9">
                                    <input type="radio" name="notify" value="notification" required>
                                    <label for="notification">Notification</label><br>
                                    <input type="radio" name="notify" value="email" required>
                                    <label for="email">Email</label><br>
                                </div>
                            </div>

                            <div class="div_notification" style="display: none">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Title:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="Title" name="title">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Message:</label>
                                    <div class="col-lg-9">
                                        {{-- <textarea class="ck_editor" name="post" id="" cols="30" rows="10"></textarea> --}}
                                        <textarea name="message" class="form-control"  placeholder="Notification Message"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Type:</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" tabindex="-1" aria-hidden="true" name="type">
                                            <option value="" disabled selected>Select Notification Type</option>
                                            <option value="payment">Payment</option>
                                            <option value="chat">Chat</option>
                                            <option value="request">Reachup Request</option>
                                        </select>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="div_email" style="display: none">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Subject:</label>
                                    <div class="col-lg-9">
                                        {{-- <textarea name="email_content" cols="30" rows="10"></textarea> --}}
                                    <input name="subject" value="" type="text" class="form-control">
                                    </div> 
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Body:</label>
                                    <div class="col-lg-9">
                                        {{-- <textarea name="email_content" cols="30" rows="10"></textarea> --}}
                                    <textarea id="editor{{ $user->id }}" name="email_content" data-notId="{{ $user->id }}"></textarea>
                                    </div> 
                                </div>
                                
                            </div>
                            <div class="div_file" style="display: none" >
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Attach File:</label>
                                    <div class="col-lg-9">
                                        <div class="uniform-uploader">
                                            <input type="file" class="form-input-styled" data-fouc="" name="image">
                                            {{-- <span class="filename" style="user-select: none;">No file selected</span><span class="action btn bg-pink-400 legitRipple" style="user-select: none;">Choose File</span> --}}
                                        </div>
                                        {{-- <span class="form-text text-muted">Accepted formats: gif, png, jpg. Max file size 2Mb</span> --}}
                                    </div>
                                </div>
                            </div>
                                
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



          <!-- Review Advisor Modal -->
        <div class="modal fade" id="reviewAdvisorModal{{ $user->id }}" tabindex="-1"
            role="dialog" aria-labelledby="reviewAdvisorModalLabel{{ $user->id }}"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reviewAdvisorModalLabel{{ $user->id }}">
                            Review Advisor {{ $user->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if($user->advisor)
                            
                        <form action="{{ route('update_advisor_status', $user->id) }}" method="POST">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Current Status:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" placeholder="John Smith" 
                                        value="{{ $user->advisor->status ?? '' }}" disabled>
                                </div>
                            </div>
                            @if(isset($user->advisor))
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Change Advisor Status</label>
                                    <div class="col-lg-9">
                                        <select class="form-control" tabindex="-1" aria-hidden="true" name="advisor_status">
                                            <option value="" disabled>Select Advisor Status</option>
                                            <option value="pending" @if($user->advisor->status == 'pending') selected @endif>Pending</option>
                                            <option value="active" @if($user->advisor->status == 'active') selected @endif>Active</option>
                                            <option value="declined" @if($user->advisor->status == 'declined') selected @endif>Declined</option>
                                        </select>
                                    </div>
                                </div>
                                @endif
                                
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                        @else
                            <span>This user has incomplete profile</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- User Detail Modal -->
        <div class="modal fade" id="userDetailModal{{ $user->id }}" tabindex="-1"
            role="dialog" aria-labelledby="userDetailModalLabel{{ $user->id }}"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userDetailModalLabel{{ $user->id }}">
                            User Detail {{ $user->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="content">
                            <div class="card">
                                <div class="card-header header-elements-inline">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item"><a href="#basic-rounded-tab1{{ $user->id }}" class="nav-link rounded-top active" data-toggle="tab">Personal</a></li>
                                        @if(count($user->ratings) > 0)
                                        <li class="nav-item"><a href="#basic-rounded-tab2{{ $user->id }}" class="nav-link rounded-top" data-toggle="tab">Ratings <span class="badge badge-default badge-pill ml-2">{{ count($user->ratings) }}</span></a></li>
                                        @endif
                                        @if(count($user->follows) > 0)
                                        <li class="nav-item"><a href="#basic-rounded-tab3{{ $user->id }}" class="nav-link rounded-top" data-toggle="tab">Followers <span class="badge badge-default badge-pill ml-2">{{ count($user->follows) }}</span></a></li>
                                        @endif
                                        @if(count($user->followers) > 0)
                                        <li class="nav-item"><a href="#basic-rounded-tab4{{ $user->id }}" class="nav-link rounded-top" data-toggle="tab">Following <span class="badge badge-default badge-pill ml-2">{{ count($user->followers) }}</span></a></li>
                                        @endif
                
                                        {{-- @if($category->have_subcategories == 'Yes')
                                        <li class="nav-item"><a href="#basic-rounded-tab2" class="nav-link rounded-top" data-toggle="tab">Sub Categories</a></li>
                                        @endif --}}
                                        
                                    </ul>
                
                                    @include('layouts.errors')
                                </div>
                
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="basic-rounded-tab1{{ $user->id }}">
                
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Current avatar:</label>
                                                
                                                <div class="image text-center"><img src="{{ $user->image }}" class="img-circle" alt="User Image" height="100" width="100"> </div>
                                            </div>

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
                    
                                        </div>
                                        <div class="tab-pane fade" id="basic-rounded-tab2{{ $user->id }}">
                                            <div class="d-flex row">
                                                @foreach ($user->ratings as $rating)    
                                                <div class="card card-body text-center col-5 m-4" style="height: 150px; border-radius:30px;">
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
                                        </div>  
                
                                        <div class="tab-pane fade" id="basic-rounded-tab3{{ $user->id }}">
                                            <div class="d-flex row">
                                                @foreach ($user->follows as $follows)    
                                                <div class="card card-body text-center col-5 m-4" style="height: 150px; border-radius:30px;">
                                                    <div class="mb-3">
                                                        <h5 class="font-weight-semibold mb-0 mt-1">
                
                                                        </h5>
                                                        {{-- <span class="d-block">{{ $follows->follower_user->name }}</span> --}}
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
                                        </div>  
                
                                        <div class="tab-pane fade" id="basic-rounded-tab4{{ $user->id }}">
                                            <div class="d-flex row">
                                                @foreach ($user->followers as $follower)    
                                                <div class="card card-body text-center col-5 m-4" style="height: 150px; border-radius:30px;">
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
                                        </div>  

                                        <div class="tab-pane fade" id="basic-rounded-tab5{{ $user->id }}">
                                            <div class="d-flex row">
                                                @foreach ($user->devices as $key => $device)    
                                                <div class="card card-body text-center col-5 m-4" style="height: 150px; border-radius:30px;">
                                                    <div class="mb-3">
                                                        <h5 class="font-weight-semibold mb-0 mt-1">
                                                            {{ $key+1 }}
                                                        </h5>
                
                                                        <span class="d-block">
                                                            @php
                                                                $rating = $follower->following_user->avg_rating ?? 0
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
                                        </div>  
                                    </div>
                                </div>
                            </div>
                            {{-- </div> --}}
                            <!-- /.content -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
                  @endforeach
                </div>
            </div>
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


@endsection


@section('javascript')
    <script>
        function deleteFunction() {
            if(!confirm("Are You Sure to delete this"))
            event.preventDefault();
        }
        $(document).on('show.bs.modal', '.notifyModal', function(e) {
            var notifyid = $(this).data('notifyid');
            $('.div_notification').hide();
            $('.div_email').hide();
            $('.div_file').hide();
            $("input[name='notify']").prop("checked", false);
            ClassicEditor
                .create(document.querySelector('#editor' + notifyid), {
                    ckfinder: {
                        uploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json',
                    },
                    toolbar: ['ckfinder', '|', 'heading', '|', 'bold', 'italic', '|', 'undo', 'redo']
                })
                .then(editor => {
                    console.log(editor);
                })
                .catch(error => {
                    console.error(error);
                });


            // alert('hi');
        })

        $(document).on('hidden.bs.modal', '.notifyModal', function(e) {
            var notifyid = $(this).data('notifyid');
            // const data = editor.getData();
            document.querySelector('.ck-editor__editable').ckeditorInstance.destroy()
            // var editor = $(this).find('#editor'+notifyid);




            // alert('hi');
        })
        // CKEDITOR.replace( "editor");
        $("input[name='notify']").click(function() {
            console.log($(this).val());
            if ($(this).val() === 'notification') {
                $(".div_notification").show();
                $('.div_email').hide();
                $('.div_file').show();
            } else {
                $('.div_email').show();
                $(".div_notification").hide();
                $('.div_file').show();
            }
        });


        $('#submit-multiple-notify').on('click', function(e) {

            e.preventDefault();
            var $inputs1 = $('#mulitple-notify-form :input');
            var $inputs2 = $('#search-form :input');

            // not sure if you wanted this, but I thought I'd add it.
            // get an associative array of just the values.
            var data = {};
            var filter = {};
            $inputs1.each(function() {
                data[this.name] = $(this).val();
            });
            $inputs2.each(function() {
                filter[this.name] = $(this).val();
            });
            console.log(data);

            $.ajax({
                    /* the route pointing to the post function */
                url: "{{ route('notify_multiple_users') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
                },
                /* send the csrf-token and the input to the controller */
                data: {data, filter},
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function (data) { 
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: data.message ?? '',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 1200);
                    console.log(data);
                    // $(".writeinfo").append(data.msg); 
                }
            }); 
        });




        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('allUsers') }}",
                type: 'GET',
                data: function(d) {
                    d.name = $('#name').val();
                    d.email = $('#email').val();
                    d.user_type = $('#user_type').val();
                    d.status = $('#status').val();
                    d.following_from = $('#following_from').val();
                    d.following_to = $('#following_to').val();
                    d.follower_from = $('#follower_from').val();
                    d.follower_to = $('#follower_to').val();
                    d.rating_from = $('#rating_from').val();
                    d.rating_to = $('#rating_to').val();
                }
            },
            columns: [
                { data: 'id', name: 'id', 'visible': false},
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'user_type',
                    name: 'user_type.name',
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'advisor_status',
                    name: 'advisor_status',
                    searchable: false
                },
                {
                    data: 'average_rating',
                    name: 'average_rating',
                    searchable: false
                },
                {
                    data: 'total_spent',
                    name: 'total_spent',
                    searchable: false
                },
                {
                    data: 'total_followers',
                    name: 'total_followers',
                    searchable: false
                },
                {
                    data: 'total_followings',
                    name: 'total_followings',
                    searchable: false
                },
                // {
                //     data: 'action',
                //     name: 'action',
                //     orderable: false,
                //     searchable: false
                // },
            ],
            'searching': false,
            'pageLength': 50,
            'ordering': false,
        });

        $('#btnFiterSubmitSearch').click(function() {
            $('.data-table').DataTable().draw(true);
        });


        function onlyNumbers(num){
            if ( /[^0-9]+/.test(num.value) ){
                num.value = num.value.replace(/[^0-9]*/g,"")
            }
        }


        $('#user-data-table tbody').on('click', 'tr', function () {
            let id = table.row( this ).id();
            let is_advisor = table.row( this ).data();
            
            $(".modal-body #action-view").attr('data-target','#userDetailModal'+id);
            $(".modal-body #action-notify").attr('data-target','#notifyUserModal'+id);
            $(".modal-body #action-notify").attr('data-notifyid',id);
            if(is_advisor.user_type == 'advisor'){
                $(".modal-body #action-status").attr('data-target','#reviewAdvisorModal'+id);
                $('#action-status').css('display', 'block');
            } else {
                $('#action-status').css('display', 'none');
            }
            $(".modal-body #action-edit").attr('href','edit_user/'+id);
            $(".modal-body #action-delete").attr('href','delete_user/'+id);
            $(".modal-body #action-delete").attr('onclick','deleteFunction()');
            $('#viewActionModal').modal('show');
            
        });

        $('.action').click(function() {
            $('#viewActionModal').modal('hide');
        });


        

        // CKEDITOR.replace( 'editor1' );

    </script>
@endsection
