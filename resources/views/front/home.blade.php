<!DOCTYPE html>
<html class="no-js" lang="en_AU"/>
<head>
<header style="background-color: #3d4d28;">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>BuyIn & SellOut</title>
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, user-scalable=no" />
	<meta name="HandheldFriendly" content="True" />
	<meta name="pinterest" content="nopin" />
	<meta property="og:locale" content="en_AU" />
	<meta property="og:type" content="website" />
    <meta property="fb:admins" content="" />
	<meta property="fb:app_id" content="" />
	<meta property="og:site_name" content="" />
	<meta property="og:title" content="" />
	<meta property="og:description" content="" />
	<meta property="og:url" content="" />
	<meta property="og:image" content="" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta property="og:image:width" content="" />
	<meta property="og:image:height" content="" />
	<meta property="og:image:alt" content="" />
	<meta name="twitter:title" content="" />
	<meta name="twitter:site" content="" />
	<meta name="twitter:description" content="" />
	<meta name="twitter:image" content="" />
	<meta name="twitter:image:alt" content="" />
	<meta name="twitter:card" content="summary_large_image" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <link rel="icon" href="{{ secure_asset('favicon.png') }}" type="image/x-icon"> --}}

	<link rel="stylesheet" type="text/css" href="{{ secure_asset('front-assets/css/slick.css')}}" />
	<link rel="stylesheet" type="text/css" href="{{ secure_asset('front-assets/css/slick-theme.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{ secure_asset('front-assets/css/style.css')}}" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&family=Raleway:ital,wght@0,400;0,600;0,800;1,200&family=Roboto+Condensed:wght@400;700&family=Roboto:wght@300;400;700;900&display=swap" rel="stylesheet"><!-- Fav Icon -->
	<link rel="shortcut icon" type="image/x-icon" href="#" />

	{{-- <div class="container"> --}}
		<nav class="navbar navbar-expand-xl" id="navbar">
			<a href="{{ route('front.home') }}" class="text-decoration-none mobile-logo">
                {{--<img src="{{ asset('front-assets/images/Logo-bs.png')}}" class="logo-sizing-mobile">--}}
				{{-- <span class="h2 text-uppercase text-primary bg-dark">BuyIn &#38;</span> --}}
				{{-- <span class="h2 text-uppercase text-primary bg-dark">&#38;</span>  --}}
				{{-- <span class="h2 text-uppercase text-white px-2">SellOut</span> --}}
			</a>
			{{-- <button class="navbar-toggler menu-btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				  <i class="navbar-toggler-icon fas fa-bars"></i>
    		</button>
    		<div class="collapse navbar-collapse" id="navbarSupportedContent">
      			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @if(getCategories()->isNotEmpty())
                        @foreach(getCategories() as $category)
                            <li class="nav-item dropdown">
                                <button class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $category->name }}
                                </button>
                                @if($category->sub_category->isNotEmpty())
                                    <ul class="dropdown-menu dropdown-menu-dark">
                                        @foreach($category->sub_category as $subCategory)
                                            <li><a class="dropdown-item nav-link" href="{{ route('sub-categories.index', $category->id) }}">{{ $subCategory->name }}</a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div> --}}
                {{-- <div class="right-nav py-0">
                    <a href="{{ route('front.cart') }}" class="ml-3 d-flex pt-2">
                        <i class="fas fa-shopping-cart text-primary"></i>					
                    </a>
                </div> 		 --}}
      	</nav>
  	{{-- </div> --}}
      <body data-instant-intensity="mousedown">
<div class="bg-light top-header">        
	<div class="container">
		<div class="row align-items-center py-3 d-none d-lg-flex justify-content-between">
			<div class="col-lg-5 logo">
				<a href="{{ route('front.home')}}" class="text-decoration-none">
                     <img src="{{ asset('front-assets/images/primarylogo.png')}}" class="logo-sizing">            
				</a>
			</div>
			<div class="col-lg-6 col-6 text-left  d-flex justify-content-end align-items-center">
				@if (Auth::check())
					<a href="{{route('account.profile')}}" class="nav-link text-dark">My Account</a>
					<a href="{{ route('front.cart') }}" class="ml-3 d-flex pt-0">
						<i class="fas fa-shopping-cart text-primary"></i>					
					</a>
				@else
					<a href="{{route('account.login')}}" class="nav-link text-dark">Login/Register</a>
					<a href="{{ route('front.cart') }}" class="ml-3 d-flex pt-0">
						<i class="fas fa-shopping-cart text-primary"></i>					
					</a>
				@endif
			</div>			
		</div>
	</div>
</div>
</header>
    <main>
    <section class="section-1">
        <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="false">
            <div class="carousel-inner">

                <div class="carousel-item active">
                    <picture>
                        <source media="(max-width: 799px)" srcset="{{ asset('front-assets/images/upang-students.jpg')}}" />
                        <source media="(min-width: 800px)" srcset="{{ asset('front-assets/images/upang-students.jpg')}}" />
                        <img src="{{ asset('front-assets/images/upang-students.jpg')}}" alt="" />
                    </picture>
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3">
                            <h1 class="display-4 text-white mb-3">Student Trends</h1>
                        </div>
                    </div>
                </div>  

                <div class="carousel-item">
                    
                    <picture>
                        <source media="(max-width: 799px)" srcset="{{ asset('front-assets/images/upang-group-students-shs.jpg')}}" />
                        <source media="(min-width: 800px)" srcset="{{ asset('front-assets/images/upang-group-students-shs.jpg')}}" />
                        <img src="{{ asset('front-assets/images/upang-group-students-shs.jpg')}}" alt="" />
                    </picture>
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3">
                            <h1 class="display-4 text-white mb-3">Student Must-Haves</h1>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>
    {{-- <section class="section-3">
        <div class="container">
            <div class="section-title">
                <h2>Categories</h2>
                @if (getCategories()->isNotEmpty())
                    @foreach (getCategories() as $category)
                        <div class="row pb-3">
                            <div class="col-lg-3">
                                <div class="cat-card">
                                    <div class="left">
                                        @if ($category->image != "")
                                            <img src="{{ asset('uploads/category/thumb/'.$category->image) }}" alt="" class="img-fluid">
                                        @endif
                                            <img src="{{ asset('front-assets/images/upang-logo.png') }}" alt="" class="img-fluid">
                                    </div>
                                    <div class="right">
                     secure_                   <div class="cat-data">
                                            <h2>{{ $category->name }}</h2>
                                            <!-- <p>100 Products</p> !-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section> --}}
    <section class="section-4 pt-5">
        <div class="container">
            <div class="section-title">
                <h2>Featured Products</h2>
            </div>    
            <div class="row pb-3">
                @if ($featuredProducts->isNotEmpty())
                    @foreach ($featuredProducts as $product)
                        @php
                            $productImage = $product->product_images->first();
                        @endphp
                        <div class="col-md-3">
                            <div class="card product-card">
                                <div class="product-image position-relative">
                                    <a href="{{ route('front.product', $product->slug) }}" class="product-img">
                                        @if (!empty($productImage->image))
                                            <img class="card-img-top" src="{{ asset('uploads/product/small/'.$productImage->image) }}"/>
                                        @else
                                            <img src="{{ asset('admin_assets/img/default-150x150.png') }}"/>
                                        @endif
                                    </a>

                                    <a class="whishlist" href="" onclick="addToWishList({{$product->id}})"><i class="far fa-heart"></i></a>

                                    <div class="product-action">
                                        @if($product->track_qty == 'Yes')
                                            @if($product->qty > 0)
                                                <button type="button" class="btn btn-dark" onclick="addToCart({{$product->id}})"><i class="fas fa-shopping-cart"></i>Add To Cart</button>
                                            @else
                                                <button type="button" class="btn btn-dark" disabled><i class="fas fa-shopping-cart"></i> Out of Stock</button>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-dark" onclick="addToCart({{ $product->id }});">
                                                <i class="fa fa-shopping-cart"></i> Add To Cart
                                            </button>
                                        @endif
                                    </div>                                    
                                </div>
                                <div class="card-body text-center mt-3">
                                    <a class="h6 link" href="{{ route('front.product', $product->slug) }}">{{ $product->title }}</a>
                                    <div class="price mt-2">
                                        <span class="h5"><strong>{{ $product->price }}</strong></span>
                                        @if ($product->compare_price > 0)
                                            <span class="h6 text-underline"><del>{{ $product->compare_price }}</del></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
    <section class="section-4 pt-5">
        <div class="container">
            <div class="section-title">
                <h2>Latest Products</h2>
            </div>    
            <div class="row pb-3">
                @if ($latestProducts->isNotEmpty())
                    @foreach ($latestProducts as $product)
                        @php
                            $productImage = $product->product_images->first();
                        @endphp
                        <div class="col-md-3">
                            <div class="card product-card">
                                <div class="product-image position-relative">
                                    <a href="{{ route('front.product', $product->slug) }}" class="product-img">
                                        @if (!empty($productImage->image))
                                            <img class="card-img-top" src="{{ asset('uploads/product/small/'.$productImage->image) }}"/>
                                        @else
                                            <img src="{{ asset('admin_assets/img/default-150x150.png') }}"/>
                                        @endif
                                    </a>

                                    <a class="whishlist" href="" onclick="addToWishList({{$product->id}})"><i class="far fa-heart"></i></a>

                                    <div class="product-action">
                                        @if($product->track_qty == 'Yes')
                                            @if($product->qty > 0)
                                                <button type="button" class="btn btn-dark" onclick="addToCart({{$product->id}})"><i class="fas fa-shopping-cart"></i>Add To Cart</button>
                                                {{-- onclick="addToCart({{ $product->id }});" --}}
                                            @else
                                                <button type="button" class="btn btn-dark" disabled><i class="fas fa-shopping-cart"></i> Out of Stock</button>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-dark" onclick="addToCart({{ $product->id }});"><i class="fas fa-shopping-cart"></i>Add To Cart</button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body text-center mt-3">
                                    <a class="h6 link" href="{{route('products.index')}}">{{ $product->title }}</a>
                                    <div class="price mt-2">
                                        <span class="h5"><strong>{{ $product->price }}</strong></span>
                                        @if ($product->compare_price > 0)
                                            <span class="h6 text-underline"><del>{{ $product->compare_price }}</del></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif                           
            </div>
        </div>
    </section>
</main>
</head>
<footer style="background-color: #3d4d28;">
	<div class="container pb-5 pt-3">
		<div class="row">
			<div class="col-md-4">
				<div class="footer-card">
					<h3>Get In Touch</h3>
					<p>BuyIn & SellOut <br>
					Arellano Street, Dagupan City, Pangasinan <br>
					buyinsellout24@gmail.com <br>
					099 9999 9999</p>
				</div>
			</div>
			<div class="col-md-4">
				<div class="footer-card">
					<h3>Important Links</h3>
					<ul>
						<li><a href="{{ route('front.aboutus') }}" title="About">About</a></li>
						<li><a href="{{ route('front.contactus') }}" title="Contact Us">Contact Us</a></li>						
						{{-- <li><a href="#" title="Privacy">Privacy</a></li>
						<li><a href="#" title="Privacy">Terms & Conditions</a></li>
						<li><a href="#" title="Privacy">Refund Policy</a></li> --}}
					</ul>
				</div>
			</div>
			<div class="col-md-4">
				<div class="footer-card">
					<h3>My Account</h3>
					<ul>
						<li><a href="{{ route('account.login') }}" title="Sell">Login</a></li>
						<li><a href="{{ route('account.register') }}" title="Advertise">Register</a></li>
						<li><a href="#" title="Contact Us">My Orders</a></li>						
					</ul>
				</div>
			</div>			
		</div>
	</div>
	<div class="copyright-area">
		<div class="container">
			<div class="row">
				<div class="col-12 mt-3">
					<div class="copy-right text-center">
						<p>© Copyright 2024 BuyIn & SellOut. All Rights Reserved</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ secure_asset('front-assets/js/jquery-3.6.0.min.js')}}"></script>
<script src="{{ secure_asset('front-assets/js/bootstrap.bundle.5.1.3.min.js')}}"></script> 
<script src="{{ secure_asset('front-assets/js/instantpages.5.1.0.min.js')}}"></script>
<script src="{{ secure_asset('front-assets/js/lazyload.17.6.0.min.js')}}"></script>
<script src="{{ secure_asset('front-assets/js/slick.min.js')}}"></script>
<script src="{{ secure_asset('front-assets/js/custom.js')}}"></script>

<script>
    window.onscroll = function() {myFunction()};
    var navbar = document.getElementById("navbar");
    var sticky = navbar.offsetTop;
    function myFunction() {
        if (window.pageYOffset >= sticky) {
            navbar.classList.add("sticky")
        } else {
            navbar.classList.remove("sticky");
        }
    }
</script>

<script>
    // $(document).ready(function() {

        function addToCart(id) {
            $.ajax({
                url: '{{ route("front.addToCart") }}',
                type: 'post',
                data: {id: id},
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.status == true) {
                        window.location.href="{{ route('front.cart') }}";
                    } else {
                        alert(response.message);
                        window.location.href="{{ route('front.cart') }}";


                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    // alert('An error occurred. Please try again.');
                    window.location.href="{{ route('front.cart') }}";


                }
            });
        }

        function updateCart(rowId, newQty, redirectToCart) {
            $.ajax({
                url: "{{ route('front.updateCart') }}",
                type: 'post',
                data: { rowId: rowId, qty: newQty },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (redirectToCart) {
                        window.location.href = "{{ route('front.cart') }}";
                    }
                }
            });
        }

        function addToWishList(id) {
            $.ajax({
                url: "{{route('front.addToWishList')}}",
                type: 'post',
                data: {id:id},
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.status == true) {

                        $("#wishlistModal .modal-body").html(response.message);
                        $("#wishlistModal").modal('show');
        
                    } else {
                        window.location.href="{{ route('account.login') }}";
                        //alert(response.message);
                    }
                }
            })
        }
    // });
</script>
</body>
</html>