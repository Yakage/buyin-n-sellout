@extends('front.layouts.app')

@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                    <li class="breadcrumb-item">{{ $product->title }}</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-7 pt-3 mb-3">
        <div class="container">
            <div class="row ">
                @include('front.account.common.message')
                <div class="col-md-5">
                    <div id="product-carousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner bg-light">
                            
                        @if ($product->product_images)
                            @foreach ($product->product_images as $key => $productImage)
                            <div class="carousel-item {{ ($key == 0) ? 'active' : ''}}">
                                <img class="w-100 h-100" src="{{ asset('uploads/product/large/'.$productImage->image) }}" alt="">
                            </div>
                            @endforeach
                        @endif
                            {{-- <div class="carousel-item active">
                                <img class="w-100 h-100" src="images/product-2.jpg" alt="Image">
                            </div>
                            <div class="carousel-item">
                                <img class="w-100 h-100" src="images/product-3.jpg" alt="Image">
                            </div>
                            <div class="carousel-item">
                                <img class="w-100 h-100" src="images/product-4.jpg" alt="Image">
                            </div> --}}
                        </div>
                        <a class="carousel-control-prev" href="#product-carousel" data-bs-slide="prev">
                            <i class="fa fa-2x fa-angle-left text-dark"></i>
                        </a>
                        <a class="carousel-control-next" href="#product-carousel" data-bs-slide="next">
                            <i class="fa fa-2x fa-angle-right text-dark"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="bg-light right">
                        <h1>{{ $product->title }}</h1>
                        <div class="d-flex mb-3">
                            <!--<div class="text-primary mr-2">
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star"></small>
                                <small class="fas fa-star-half-alt"></small>
                                <small class="far fa-star"></small>
                            </div>!-->
                            <div class="star-rating product mt-2" title="70%">
                                            <div class="back-stars">
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                
                                                <div class="front-stars" style="width: {{ $avgRatingPer}} %">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                        </div>  
                            <small class="pt-2 ps-1">( {{( $product->product_rating_count > 1) ? 
                                            $product->product_rating_count. 'Reviews' : 
                                            $product->product_rating_count. 'Review'}} )</small>
                        </div>
                        @if ($product->compare_price > 0)
                        <h2 class="price text-secondary"><del>PHP {{ $product->compare_price }}</del></h2>
                        @endif
                        <h2 class="price ">PHP {{ $product->price }}</h2>
                        {!! $product->short_description !!}   
                        {{-- <form action="{{ route('front.addToCart') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-dark"><i class="fas fa-shopping-cart"></i> Add To Cart</button>
                        </form> --}}
                        <button type="button" class="btn btn-dark" onclick="addToCart({{ $product->id }});"><i class="fas fa-shopping-cart"></i>Add To Cart</button>


                    </div>
                </div>
                <div class="col-md-12 mt-5">
                    <div class="bg-light">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Description</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping" aria-selected="false">Shipping & Returns</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                            {!! $product->description !!}
                            </div>
                            <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                            {!! $product->shipping_returns !!}
                            </div>
                        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                            <div class="col-md-8">
                                <div class="row">

                                        <h3 class="h4 pb-3">Write a Review</h3>
                                        <form action="" name="productRatingForm" id="productRatingForm" method="post">
                                            @csrf
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                                            <p></p>
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" name="email" id="email" placeholder="Email">
                                            <p></p>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="rating">Rating</label>
                                            <br>
                                            <div class="rating" style="width: 10rem">
                                                <input id="rating-5" type="radio" name="rating" value="5"/><label for="rating-5"><i class="fas fa-3x fa-star"></i></label>
                                                <input id="rating-4" type="radio" name="rating" value="4"  /><label for="rating-4"><i class="fas fa-3x fa-star"></i></label>
                                                <input id="rating-3" type="radio" name="rating" value="3"/><label for="rating-3"><i class="fas fa-3x fa-star"></i></label>
                                                <input id="rating-2" type="radio" name="rating" value="2"/><label for="rating-2"><i class="fas fa-3x fa-star"></i></label>
                                                <input id="rating-1" type="radio" name="rating" value="1"/><label for="rating-1"><i class="fas fa-3x fa-star"></i></label>                                                
                                            </div>
                                            <p class="product-rating-error text-danger"></p>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="">How was your overall experience?</label>
                                            <textarea name="comment"  id="review" class="form-control" cols="30" rows="10" placeholder="How was your overall experience?"></textarea>
                                            <p></p>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-dark">Submit</button>
                                        </div>
                                    </form>   
                                    
                                </div>
                            </div>
                            <div class="col-md-12 mt-5">
                                <div class="overall-rating mb-3">
                                    <div class="d-flex">
                                        <h1 class="h3 pe-3">{{ $avgRating }}</h1>
                                        <div class="star-rating mt-2" title="70%">
                                            <div class="back-stars">
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                
                                                <div class="front-stars" style="width: {{ $avgRatingPer}} %">
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                </div>
                                            </div>
                                        </div>  
                                        <div class="pt-2 ps-2">( {{( $product->product_rating_count > 1) ? 
                                            $product->product_rating_count. 'Reviews' : 
                                            $product->product_rating_count. 'Review'}} )</div>
                                    </div>
                                    
                                </div>

                                @if($product->product_ratings->isNotEmpty())
                                @foreach ($product->product_ratings as $rating )
                                @php
                                    $ratingPer = ( $rating->rating*100)/5;

                                @endphp
                                <div class="rating-group mb-4">
                                   <span> <strong>{{$rating->username}} </strong></span>
                                    <div class="star-rating mt-2" title="">
                                        <div class="back-stars">
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                            
                                            <div class="front-stars" style="width: {{ $ratingPer }}%">
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                                <i class="fa fa-star" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>   
                                    <div class="my-3">
                                        <p>{{ $rating->comment }} </p>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                        </div>
                    </div>
                </div> 
            </div>           
        </div>
    </section>
    @if(!empty($relatedProducts))
    <section class="pt-5 section-8">
        <div class="container">
            <div class="section-title">
                <h2>Related Products</h2>
            </div> 
            <div class="col-md-12">
                <div id="related-products" class="carousel">
                    
                    @foreach( $relatedProducts as $relProduct)
                    @php
                        $productImage = $relProduct->product_images->first();
                    @endphp
                    <div class="card product-card">
                        <div class="product-image position-relative">

                            <a href="" class="product-img">
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
                                        {{-- <form action="{{ route('front.addToCart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button type="submit" class="btn btn-dark"><i class="fas fa-shopping-cart"></i> ADD TO CART</button>
                                        </form> --}}
                                        <button type="button" class="btn btn-dark add" onclick="addToCart({{ $product->id }});"><i class="fas fa-shopping-cart"></i>Add To Cart</button>
                                                {{--  --}}
                                    @else
                                        <button type="button" class="btn btn-dark" disabled><i class="fas fa-shopping-cart"></i> Out of Stock</button>
                                    @endif
                                @else
                                <button type="button" class="btn btn-dark" onclick="addToCart({{ $product->id }});"><i class="fas fa-shopping-cart"></i>Add To Cart</button>
                                {{--  --}}
                                @endif
                            </div>
                        </div>                        
                        <div class="card-body text-center mt-3">
                            <a class="h6 link" href="">{{ $relProduct->title }}</a>
                            <div class="price mt-2">
                                <span class="h5"><strong>{{ $relProduct->price }}</strong></span>
                                @if($relProduct->compare_price > 0)
                                <span class="h6 text-underline"><del></del>{{ $relProduct->compare_price }}</span>
                                @endif
                            </div>
                        </div>                        
                    </div> 
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
@endsection

@section('customJs')
    <script type="text/javascript">

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

                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    // alert('An error occurred. Please try again. add to cart');
                    alert('Product already added to cart.');
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
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('An error occurred. Please try again. update cart');
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

        $('#productRatingForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("front.saveRating", $product->id) }}',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status == false) {
                    // Handle validation errors
                    $.each(response.errors, function(key, value) {
                        $('#' + key).addClass('is-invalid').siblings('p').text(value);
                    });
                } else {
                    // Redirect after successful submission
                    window.location.href = "{{ route('front.product', $product->slug) }}";
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('An error occurred while submitting the form.');
            }
        });
    });

        var errors = response.errors;

        if(response.status == false) {
                if(errors.name) {
                $("#name").addClass('is-invalid')
                .siblings("p")
                .addClass('invalid-feedback')
                .html(errors.name);
            } else {
                $("#name").removeClass('is-invalid')
                    .siblings("p")
                    .addClass('invalid-feedback')
                    .html(''); 
            }  
            if(errors.email) {
                $("#email").addClass('is-invalid')
                .siblings("p")
                .addClass('invalid-feedback')
                .html(errors.email);
            } else {
                $("#email").removeClass('is-invalid')
                    .siblings("p")
                    .addClass('invalid-feedback')
                    .html(''); 
            }
            if(errors.comment) {
                $("#comment").addClass('is-invalid')
                .siblings("p")
                .addClass('invalid-feedback')
                .html(errors.comment);
            } else {
                $("#comment").removeClass('is-invalid')
                    .siblings("p")
                    .addClass('invalid-feedback')
                    .html(''); 
            }
            if(errors.rating) {
                $(".product-rating-error").html(errors.rating);
            } else {
                $(".product-rating-error").html('');
            }  
        } else{
            window.location.href = "{{ route('front.product',$product->slug) }}" 
        }        
    </script>
@endsection 