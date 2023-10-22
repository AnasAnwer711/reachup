<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>ReachUp</title>
<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" />

<!-- v4.0.0-alpha.6 -->
<link rel="stylesheet" href="{{ asset('dist/bootstrap/css/bootstrap.min.css') }}">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/font-awesome/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/et-line-font/et-line-font.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/themify-icons/themify-icons.css') }}">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="text-center "><img class="w-100 mb-5" src="{{ asset('dist/img/w-logo.png') }}" /></div>
  <div class="login-box-body">
    {{-- <p> </p> --}}
    @if(Session::has('error') && Session::get('error') != '')
        <div class="alert alert-danger">
        {{Session::get('error')}}
        </div>
    @endif
    @if(Session::has('success') && Session::get('success') != '')
        <div class="alert alert-success">
        {{Session::get('success')}}
        </div>
    @endif
    @if(!empty($success))
        <div class="alert alert-success"> {{ $success }}</div>
    @endif
    @if(!empty($error))
        <div class="alert alert-danger"> {{ $error }}</div>
    @endif
    @if(Session::get('success'))
        
    @else
    {{-- <form class="form-horizontal" method="POST" action="{{ url('email_confirmation') }}">
        {{ csrf_field() }}
        <input type="hidden" name="token" value="{{ $user_email }}">

        <div class="text-center mb-3">
            <h3 class="mb-0">Confirm Your Email</h3>
            <span class="d-block text-muted">Enter your email and confirm</span>
         </div>
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label for="email" class="col-md-12 control-label">Email Address</label>

            <div class="col-md-12">
                <input id="email" type="email" class="form-control" name="email" value="" required autofocus>

            </div>
        </div>

        <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
                <button type="submit" class="btn btn-primary">
                    Confirm
                </button>
            </div>
        </div>
    </form> --}}
    @endif
  </div>
  <!-- /.login-box-body --> 
</div>
<!-- /.login-box --> 

<!-- jQuery 3 --> 
<script src="{{ asset('dist/js/jquery.min.js') }}"></script> 

<!-- v4.0.0-alpha.6 --> 
<script src="{{ asset('dist/bootstrap/js/bootstrap.min.js') }}"></script> 

<!-- template --> 
<script src="{{ asset('dist/js/niche.js') }}"></script>
</body>
</html>










