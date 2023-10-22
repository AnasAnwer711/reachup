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
    <div class="container">
        <div class="text-center mx-auto mt-5" style="width: 400px"><img class="w-100 mb-5" src="dist/img/w-logo.png" /></div>
        <div class="login-box-body">

            @include('layouts.errors')
            @include('layouts.error')

            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                              <div class="d-flex">

                                <h5>Two Factor Authentication</h5>
                                <a href="{{ route('logout') }}" class="list-icons-item float-right ml-auto btn btn-primary text-white" data-action="collapse"><i class="fa fa-reply mr-2"></i> Back to Login</a>
                              </div>
                            </div>
                            <div class="card-body">
                                <p>Two factor authentication (2FA) strengthens access security by requiring two methods
                                    (also referred to as factors) to verify your identity. Two factor authentication
                                    protects against phishing, social engineering and password brute force attacks and
                                    secures your logins from attackers exploiting weak or stolen credentials.</p>
                                @if(Session::has('newSecret'))
                                1. Scan this QR code with your Google Authenticator App. Alternatively, you can use the
                                code: <code>{{ $data['secret'] ?? '' }}</code><br />
                                <img src="{{ $data['google2fa_url'] ?? '' }}" alt="">
                                <br /><br />
                                @else 
                                1. Generate new secret key
                                <form class="form-horizontal" method="GET" action="{{ route('generate2faSecret') }}">
                                  {{ csrf_field() }}
                                  <div class="form-group">
                                      <button type="submit" class="btn btn-primary">
                                          Generate Secret Key 
                                      </button>
                                  </div>
                                </form>
                                
                                @endif
                                2. Enter the pin from Google Authenticator app:<br /><br />
                                <form class="form-horizontal" method="POST" action="{{ route('2faVerify') }}">
                                    {{ csrf_field() }}
                                    {{-- <div class="form-group{{ $errors->has('verify-code') ? ' has-error' : '' }}"> --}}
                                        <label for="secret" class="control-label">Authenticator Code</label>
                                        <input id="secret" type="password" class="form-control col-md-6" name="secret"
                                            required>
                                        @if ($errors->has('verify-code'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('verify-code') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        Authenticate
                                    </button>
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
