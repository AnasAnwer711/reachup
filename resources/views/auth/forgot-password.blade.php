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
    <h3 class="login-box-msg m-b-1">Recover Password</h3>
    <p>Enter your Email and instructions will be sent to you! </p>
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
    <form action="{{ route('forgot_password') }}" method="post">
      @csrf
      <div class="form-group has-feedback">
        <input type="email" class="form-control sty1" placeholder="Email" name="email">
      </div>
      <div>
        <div class="col-xs-4 m-t-1">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
        </div>
        <!-- /.col --> 
      </div>
    </form>
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
</body>
</html>