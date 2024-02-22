<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BuyIn & SellOut :: Administrative Panel</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ secure_asset('admin_assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ secure_asset('admin_assets/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('admin_assets/css/custom.css') }}">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <!-- /.login-logo -->
    @include('admin.message')
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="#" class="h3">Administrative Panel</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Create a new admin account</p>
            <form action="{{ route('admin.store') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="text"  name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                    @error('name')
                    <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>
                <div class="input-group mb-3">
                    <input type="email"  name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @error('email')
                    <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('password')
                    <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<!-- ./wrapper -->
<!-- jQuery -->
<script src="{{ secure_asset('admin_assets/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ secure_asset('admin_assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ secure_asset('/admin_assets/js/adminlte.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
{{-- <script src="js/demo.js"></script> --}}
</body>
</html>
