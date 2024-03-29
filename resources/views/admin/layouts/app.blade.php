<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>BuyIn & SellOut :: Admin Panel</title>
		<!-- Google Font: Source Sans Pro -->
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
		<!-- Font Awesome -->
		<link rel="stylesheet" type="text/css" href="{{ secure_asset('admin_assets/plugins/fontawesome-free/css/all.min.css') }}">
		<!-- Theme style -->
		<link rel="stylesheet" type="text/css" href="{{ secure_asset('admin_assets/css/adminlte.min.css') }}">

		<link rel="stylesheet" type="text/css" href="{{ secure_asset('admin_assets/plugins/dropzone/min/dropzone.min.css') }}">

		<link rel="stylesheet" type="text/css" href="{{ secure_asset('admin_assets/plugins/summernote/summernote.min.css') }}">

		<link rel="stylesheet" type="text/css" href="{{ secure_asset('admin_assets/plugins/select2/css/select2.min.css') }}">

		<link rel="stylesheet" type="text/css" href="{{ secure_asset('admin_assets/css/datetimepicker.css') }}">

		<link rel="stylesheet" type="text/css" href="{{ secure_asset('admin_assets/css/custom.css') }}">

		<!-- Include Dropzone CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/dropzone.min.css" integrity="sha512-nwG+XC6Wfp8/V7srr1icq0U0TnGhtjl9QDQV4+mnqioSau7t+dbG0el/f6dMviTZdm8gpMPFaKc8jHGZlMtzGA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<!-- Include jQuery -->
		<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
		<!-- Include Dropzone JS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/min/dropzone.min.js" integrity="sha512-Z0FNcsLbfSAhmmX3l4y+8V3V7zckKdqeU1QqUJgMj/JLUWZ5rFRe+CFCGBvRtOmCnn+TtnmMxAqTni7BvPOYvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		
		
		<meta name="csrf-token" content="{{ csrf_token() }}">
	</head>
	<body class="hold-transition sidebar-mini">
		<!-- Site wrapper -->
		<div class="wrapper">
			<!-- Navbar -->
			<nav class="main-header navbar navbar-expand navbar-white navbar-light">
				<!-- Right navbar links -->
				<ul class="navbar-nav">
					<li class="nav-item">
					  	<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
					</li>					
				</ul>
				<div class="navbar-nav pl-2">
					<!-- <ol class="breadcrumb p-0 m-0 bg-white">
						<li class="breadcrumb-item active">Dashboard</li>
					</ol> -->
				</div>
				
				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class="nav-link" data-widget="fullscreen" href="#" role="button">
							<i class="fas fa-expand-arrows-alt"></i>
						</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link p-0 pr-3" data-toggle="dropdown" href="#">
							<img src="{{ asset('admin_assets/img/avatar5.png')}}" class='img-circle elevation-2' width="40" height="40" alt="">
						</a>
						<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-3">
							@if(Auth::check())
								<h4 class="h4 mb-0"><strong>{{ Auth::user()->name }}</strong></h4>
								<div class="mb-3">{{ Auth::user()->email }}</div>
							@endif
							{{-- <h4 class="h4 mb-0"><strong> {{ Auth::user()->name }} </strong></h4>
							<div class="mb-3">{{ Auth::user()->email }} </div> --}}
							<div class="dropdown-divider"></div>
							<a href="{{route('admin.processChangePassword')}}" class="dropdown-item">
								<i class="fas fa-user-cog mr-2"></i> Settings								
							</a>
							<div class="dropdown-divider"></div>
							<a href="{{route('admin.showChangePasswordForm')}}" class="dropdown-item">
								<i class="fas fa-lock mr-2"></i> Change Password
							</a>
							<div class="dropdown-divider"></div>
							<a href=" {{ route('admin.logout') }}" class="dropdown-item text-danger">
								<i class="fas fa-sign-out-alt mr-2"></i> Logout							
							</a>							
						</div>
					</li>
				</ul>
			</nav>
			<!-- /.navbar -->
			<!-- Main Sidebar Container -->
			@include('admin.layouts.sidebar')
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				@yield('content')
			</div>
			<!-- /.content-wrapper -->
			<footer class="main-footer">
				
				<strong>Copyright &copy; 2024 BuyIn & SellOut All rights reserved.
			</footer>
			
		</div>
		<!-- ./wrapper -->
		<!-- jQuery -->
		<script src="{{ secure_asset('admin_assets/plugins/jquery/jquery.min.js') }}"></script>
		<!-- Bootstrap 4 -->
		<script src="{{ secure_asset('admin_assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
		<!-- AdminLTE App -->
		<script src="{{ secure_asset('admin_assets/js/adminlte.min.js') }}"></script>

		<script src="{{ secure_asset('admin_assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
        
		<script src="{{ secure_asset('admin_assets/plugins/summernote/summernote.min.js') }}"></script>

		<script src="{{ secure_asset('admin_assets/plugins/select2/js/select2.min.js') }}"></script>

		<script src="{{ secure_asset('admin_assets/js/datetimepicker.js') }}"></script>

		{{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> --}}


		<!-- AdminLTE for demo purposes -->
		<script src="{{ secure_asset('admin_assets/js/demo.js') }}"></script>

		<script type="text/javascript">
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$(document).ready(function(){
				$(".summernote").summernote({
					height:250
				});
			})
		</script>

        @yield('customJs')
	</body>
</html>