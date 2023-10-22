@extends('layouts.app')

@section('css')

    <style>
        .cardStyle{
            border: 1px solid #ccc;
            padding: 20px;
        }
        .bdash-orng-right{
            border-right: 1px dashed #f39c12;
        }
        .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
    </style>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header sty-one">
            <h1 class="text-black">Default Setting</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="sub-bread"><i class="fa fa-angle-right"></i> <a href="{{ route('setting.index') }}">Settings</a></li>
                {{-- <li><i class="fa fa-angle-right"></i> Add</li> --}}
            </ol>
        </div>
        <!-- Main content -->
        <div class="content">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a href="#basic-rounded-tab1" class="nav-link rounded-top active" data-toggle="tab">Basic</a></li>
                        <li class="nav-item"><a href="#basic-rounded-tab2" class="nav-link rounded-top" data-toggle="tab">Rules</a></li>
                        {{-- <li class="nav-item"><a href="#basic-rounded-tab3" class="nav-link rounded-top" data-toggle="tab">Additional Charges</a></li> --}}
                        
                    </ul>
                    {{-- <h5 class="card-title">@if(isset($setting)) Edit @else Add New @endif </h5> --}}
                    @include('layouts.errors')
                    @include('layouts.success')
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="basic-rounded-tab1">
                            @if(isset($setting))
                                <form action="{{ route('setting.update', $setting->id) }}" method="POST" enctype="multipart/form-data">
                            @else
                                <form action="{{ route('setting.store') }}" method="POST" enctype="multipart/form-data">
                            @endif
                                @csrf
                                @if(isset($setting))
                                    @method('PUT')
                                @endif
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Session Hour:</label>
                                    <div class="col-lg-9">
                                        <input type="number" class="form-control" placeholder="Enter no of session hour" name="session_hour" min="0" value="{{ $setting->session_hour ?? '' }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Client Id:</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="hidden" name="client_check" id="client_check" value="0">
                                            <input type="text" class="form-control"  @if($default_set) disabled @endif placeholder="Enter client id" name="client_id" id="client_id" min="0" value="{{ $setting->client_id ?? '' }}">
                                            @if($default_set)
                                            <span class="input-group-append p-2 btn-primary" id="client_key">
                                                <span class="input-group-text"><i class="icon-client fa @if($default_set) fa-edit @else fa-times @endif icon"></i></span>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Secret Id:</label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <input type="hidden" name="secret_check" id="secret_check" value="0">
                                            <input type="text" class="form-control" @if($default_set) disabled @endif placeholder="Enter secret id" name="secret_id" id="secret_id" min="0" value="{{ $setting->secret_id ?? '' }}">
                                            @if($default_set)
                                            <span class="input-group-append p-2 btn-primary" id="secret_key">
                                                <span class="input-group-text"><i class="fa @if($default_set) fa-edit @else fa-times @endif icon-secret"></i></span>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary legitRipple"> Save Configuration </button>
                                </div>
                            </form>
                            @if(isset($setting))
                            <form action="{{ route('additional-charges', $setting->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <hr>
                                <div class="d-flex">
                                    
                                    <h3>Additional Charges</h3>
                                    <!-- Rounded switch -->
                                    <label class="switch">
                                        <input class="additional_charges" name="is_additional_charges" type="checkbox" @if($setting->is_additional_charges) checked @endif>
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                <div class="setup_charges" style="display:@if($setting->is_additional_charges) block @else none @endif">

                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Title:</label>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" placeholder="Enter charges title" name="title" value="{{ $setting->title ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Description:</label>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" placeholder="Enter description of charges" name="description" value="{{ $setting->description ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Percentage:</label>
                                        <div class="col-lg-9">
                                            <input type="number" class="form-control" placeholder="Enter no of percentage" name="percentage" min="0" value="{{ $setting->percentage ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary legitRipple"> Save Charges </button>
                                </div>
                            </form>
                            @endif
                                                             
                                
                        </div>
                        <div class="tab-pane fade" id="basic-rounded-tab2">
                            <h3>Default Rule</h3>
                            <div class="cardStyle">
                                <form action="{{ route('default_percentage') }}" method="POST">
                                    @csrf
                                    @foreach ($default_rules as $dr)
                                        
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label">{{ ucfirst($dr->concern) }} Percentage:</label>
                                        <div class="col-lg-9">
                                            <input type="number" class="form-control" placeholder="Enter no of percentage" name="{{ $dr->concern }}_percentage" min="1" value="{{ $dr->percentage ?? '' }}">
                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary legitRipple"> Update </button>
                                    </div>
                                </form>

                            </div>
                            
                            <hr>
                            <h3>Cancellation Rule</h3>
                            <small>This rule will be applied once advisor have accepted reachup request</small>
                            <div class="cardStyle">
                                <div class="actionByUser">
                                    <form action="{{ route('cancel_before_percentage') }}" method="POST">
                                        @csrf
                                        <h4 class="text-center font-weight-bold mb-4 p-2">BEFORE 48 HOURS</h4>
                                        <div class="col-lg-12 d-flex">
                                            <div class="col-lg-6 bdash-orng-right">
                                                <h5 class="font-weight-bold mb-4 py-2">Initiated By User</h5>
                                                @foreach ($user_before_cancel_rules as $ubcr)
                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">{{ ucfirst($ubcr->concern) }} Percentage:</label>
                                                        <div class="col-lg-9">
                                                            <input type="number" class="form-control" placeholder="Enter no of percentage" name="{{ $ubcr->concern }}_user_percentage" value="{{ $ubcr->percentage ?? '' }}">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="col-lg-6">
                                                <h5 class="font-weight-bold mb-4 py-2">Initiated By Advisor</h5>
                                                @foreach ($advisor_before_cancel_rules as $abcr)
                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">{{ ucfirst($abcr->concern) }} Percentage:</label>
                                                        <div class="col-lg-9">
                                                            <input type="number" class="form-control" placeholder="Enter no of percentage" name="{{ $abcr->concern }}_advisor_percentage" value="{{ $abcr->percentage ?? '' }}">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div> 
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary legitRipple"> Update </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="actionByAdvisor">
                                    <form action="{{ route('cancel_after_percentage') }}" method="POST">
                                        @csrf
                                        <h4 class="text-center font-weight-bold my-4 p-2">AFTER 48 HOURS</h4>
                                        <div class="col-lg-12 d-flex">
                                            <div class="col-lg-6 bdash-orng-right">
                                                <h5 class="font-weight-bold mb-4 py-2">Initiated By User</h5>
                                                @foreach ($user_after_cancel_rules as $uacr)
                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">{{ ucfirst($uacr->concern) }} Percentage:</label>
                                                        <div class="col-lg-9">
                                                            <input type="number" class="form-control" placeholder="Enter no of percentage" name="{{ $uacr->concern }}_user_percentage" value="{{ $uacr->percentage ?? '' }}">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="col-lg-6">
                                                <h5 class="font-weight-bold mb-4 py-2">Initiated By Advisor</h5>
                                                @foreach ($advisor_after_cancel_rules as $aacr)
                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">{{ ucfirst($aacr->concern) }} Percentage:</label>
                                                        <div class="col-lg-9">
                                                            <input type="number" class="form-control" placeholder="Enter no of percentage" name="{{ $aacr->concern }}_advisor_percentage" value="{{ $aacr->percentage ?? '' }}">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div> 
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary legitRipple"> Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                        {{-- <div class="tab-pane fade" id="basic-rounded-tab3">
                            @if(isset($setting))
                                <form action="{{ route('setting.update', $setting->id) }}" method="POST" enctype="multipart/form-data">
                            @else
                                <form action="{{ route('setting.store') }}" method="POST" enctype="multipart/form-data">
                            @endif
                                @csrf
                                @if(isset($setting))
                                    @method('PUT')
                                @endif
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Title:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="Enter charges title" name="title" value="{{ $setting->title ?? '' }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Description:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" placeholder="Enter description of charges" name="charges" value="{{ $setting->description ?? '' }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Percentage:</label>
                                    <div class="col-lg-9">
                                        <input type="number" class="form-control" placeholder="Enter no of percentage" name="percentage" min="0" value="{{ $setting->percentage ?? '' }}">
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary legitRipple"> Save </button>
                                </div>
                            </form>
                        </div> --}}
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

            $('#client_key').click(function(){
                let c = $('#client_id').prop('disabled', function(i, v) { return !v; });
                if(c.attr('disabled')){
                    $('.icon-client').removeClass('fa-times');
                    $('.icon-client').addClass('fa-edit');
                    $('#client_check').val('0');
                } else {
                    $('.icon-client').removeClass('fa-edit');
                    $('.icon-client').addClass('fa-times');
                    $('#client_check').val('1');

                }

                console.log(c.attr('disabled'));
            });
            $('#secret_key').click(function(){
                let s = $('#secret_id').prop('disabled', function(i, v) { return !v; });
                if(s.attr('disabled')){
                    $('.icon-secret').removeClass('fa-times');
                    $('.icon-secret').addClass('fa-edit');
                    $('#secret_check').val('0');
                } else {
                    $('.icon-secret').removeClass('fa-edit');
                    $('.icon-secret').addClass('fa-times');
                    $('#secret_check').val('1');
                }
            });

            $('.additional_charges').click(function() {
                $(".setup_charges").toggle(this.checked);
            });
        });
    </script>
@endsection
