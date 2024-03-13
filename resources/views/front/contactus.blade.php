@extends('front.layouts.app')

@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                <li class="breadcrumb-item">Contact Us</li>
            </ol>
        </div>
    </div>
</section>

<section class=" section-10">
    <div class="container">
        <div class="section-title mt-5 ">
            <h2>Love to Hear From You</h2>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="row">
            <div class="col-md-6 mt-3 pe-lg-5">
                <p>We value your feedback and are always here to assist you. If you have any questions, concerns, or suggestions, please don't hesitate to reach out to us. Our dedicated support team is ready to provide you with the assistance you need.</p>
                <address>
                    BuyIn & SellOut <br>
                    Arellano Street<br>
                    Dagupan City, Pangasinan<br>
                    <a href="tel:+xxxxxxxx">09 999 9999</a><br>
                    <a href="#">buyinsellout24@gmail.com</a>
                </address>
            </div>

            <div class="col-md-6">
                <form class="shake" role="form" method="post" id="contactForm" name="contact-form" action="{{ route('front.processContactUs') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="mb-2" for="name">Name</label>
                        <input class="form-control" id="name" type="text" name="name" required data-error="Please enter your name">
                        <div class="help-block with-errors"></div>
                    </div>

                    <div class="mb-3">
                        <label class="mb-2" for="email">Email</label>
                        <input class="form-control" id="email" type="email" name="email" required data-error="Please enter your Email">
                        <div class="help-block with-errors"></div>
                    </div>

                    <div class="mb-3">
                        <label class="mb-2">Subject</label>
                        <input class="form-control" id="msg_subject" type="text" name="subject" required data-error="Please enter your message subject">
                        <div class="help-block with-errors"></div>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="mb-2">Message</label>
                        <textarea class="form-control" rows="3" id="message" name="message" required data-error="Write your message"></textarea>
                        <div class="help-block with-errors"></div>
                    </div>

                    <div class="form-submit">
                        <button class="btn btn-dark" type="submit" id="form-submit"><i class="material-icons mdi mdi-message-outline"></i> Send Message</button>
                        <div id="msgSubmit" class="h3 text-center hidden"></div>
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
