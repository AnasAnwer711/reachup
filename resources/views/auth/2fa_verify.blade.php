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

            <h3 class="login-box-msg">Two Factor Authentication</h3>
            @include('layouts.errors')
            @include('layouts.error')

            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="col-md-8 ">
                        <div class="card">
                            <div class="card-header">Two Factor Authentication</div>
                            <div class="card-body">
                                <p>Two factor authentication (2FA) strengthens access security by requiring two methods (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.</p>
        
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                Enter the pin from Google Authenticator app:<br/><br/>
                                <form class="form-horizontal" action="{{ route('2faVerify') }}" method="POST">
                                    {{ csrf_field() }}
                                    {{-- <div class="form-group{{ $errors->has('one_time_password-code') ? ' has-error' : '' }}"> --}}
                                        <label for="one_time_password" class="control-label">One Time Password</label>
                                        <input id="one_time_password" name="one_time_password" class="form-control col-md-4"  type="text" required/>
                                    </div>
                                    <button class="btn btn-primary" type="submit">Authenticate</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
</body>

</html>
