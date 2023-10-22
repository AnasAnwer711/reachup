<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>ReachUp Dashboard</title>
<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" />

<!-- v4.0.0-alpha.6 -->
<link rel="stylesheet" href="dist/bootstrap/css/bootstrap.min.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

<!-- Theme style -->
<link rel="stylesheet" href="dist/css/style.css">
<link rel="stylesheet" href="dist/css/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="dist/css/et-line-font/et-line-font.css">
<link rel="stylesheet" href="dist/css/themify-icons/themify-icons.css">

<!-- FAV ICON -->
<link rel="shortcut icon" type="image/x-icon" href="dist/img/fav-icon.png">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="text-center "><img class="w-100 mb-5" src="dist/img/w-logo.png" /></div>
  <div class="login-box-body">

    <h3 class="login-box-msg">Sign In</h3>
    @include('layouts.errors')
    @include('layouts.error')

    <form action="{{ route('login') }}" method="post">
      @csrf
      <div class="form-group has-feedback">
        <input type="text" class="form-control sty1" placeholder="Username or Email" name="user" value="{{ old('user') }}">
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control sty1" placeholder="Password" name="password" id="password">
        <input type="checkbox" class=""  onclick="myFunction()"><span> Show Password</span>

      </div>
      <div>
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label> <input type="checkbox" name="remember_me">
              Remember Me </label>
            <a href="{{ route('forgot_password') }}" class="pull-right"><i class="fa fa-lock"></i> Forgot Password?</a> </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4 m-t-1">
          {{-- <a href="{{ route('dashboard') }}" class="btn btn-primary btn-block btn-flat">Sign In</a> --}}
          <button class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        <!-- /.col --> 
      </div>
    </form>
   
    <!-- /.social-auth-links -->
    
    {{-- <div class="m-t-2">Don't have an account? <a href="pages-register.html" class="text-center">Sign Up</a></div> --}}
  </div>
  <!-- /.login-box-body --> 
</div>
<!-- /.login-box --> 

<!-- jQuery 3 --> 
<script src="dist/js/jquery.min.js"></script> 

<!-- v4.0.0-alpha.6 --> 
<script src="dist/bootstrap/js/bootstrap.min.js"></script> 

<!-- template --> 
<script src="dist/js/niche.js"></script>
<script>
  function myFunction() {
    var x = document.getElementById("password");
    if (x.type === "password") {
      x.type = "text";
    } else {
      x.type = "password";
    }
  }
  </script>
</body>
</html>