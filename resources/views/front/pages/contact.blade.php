@extends('front.layout.layout')
@section('content')
<!-- Page Introduction Wrapper -->
<div class="page-style-a">
    <div class="container">
        <div class="page-intro">
            <h2>Contact Us</h2>
            <ul class="bread-crumb">
                <li class="has-separator">
                    <i class="ion ion-md-home"></i>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li class="is-marked">
                    <a href="#">Contact Us</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Page Introduction Wrapper /- -->
<!-- Contact-Page -->
<div class="page-contact u-s-p-t-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="touch-wrapper">
                    <h1 class="contact-h1">Get In Touch With Us</h1>
                    @if(Session::has('success_message'))
                      <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success: </strong> <?php echo Session::get('success_message'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      @endif
                      @if($errors->any())
                          <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error: </strong> <?php echo implode('', $errors->all('<div>:message</div>')); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                        @endif
                    <form action="{{ url('contact') }}" method="post">@csrf
                        <div class="group-inline u-s-m-b-30">
                            <div class="group-1 u-s-p-r-16">
                                <label for="contact-name">Your Name
                                    <span class="astk">*</span>
                                </label>
                                <input type="text" id="contact-name" class="text-field" placeholder="Name" name="name" value="{{ old('name') }}" required="">
                            </div>
                            <div class="group-2">
                                <label for="contact-email">Your Email
                                    <span class="astk">*</span>
                                </label>
                                <input type="email" id="contact-email" class="text-field" placeholder="Email" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="u-s-m-b-30">
                            <label for="contact-subject">Subject
                                <span class="astk">*</span>
                            </label>
                            <input type="text" id="contact-subject" class="text-field" placeholder="Subject" name="subject" value="{{ old('subject') }}" required>
                        </div>
                        <div class="u-s-m-b-30">
                            <label for="contact-message">Message:</label>
                            <span class="astk">*</span>
                            <textarea class="text-area" id="contact-message" name="message" required="">{{ old('message') }}</textarea>
                        </div>
                        <div class="u-s-m-b-30">
                            <button type="submit" class="button button-outline-secondary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="information-about-wrapper">
                    <h1 class="contact-h1">Information About Us</h1>
                    <p>
                        Welcome to our website! 
                    </p>
                    <p>
                        We are here to introduce The Western Mindanao State University Technology Business Incubator (WMSU-TBI) is a university-based initiative that supports start-up and early-stage MSMEs in the Zamboanga Peninsula through incubation and acceleration services.
                    <p>
                        The WMSU-TBI aims to assist MSMEs in the agriculture industry's growth and success by providing access to training, mentorship, and other resources. Despite the support extended by WMSU-TBI, many MSMEs in the region continue to need help in adopting and utilizing e-commerce platforms. The need for more user-friendly e-commerce solutions customized to the specific needs and characteristics of MSMEs in the agriculture industry may contribute to this struggle.
                    </p>
                    
                </div>
                <div class="contact-us-wrapper">
                    <h1 class="contact-h1">Contact Us</h1>
                    <div class="contact-material u-s-m-b-16">
                        <h6>Location</h6>
                        <span>WMSU TBI</span>
                        <span>Zamboanga City, Philippines</span>
                    </div>
                    <div class="contact-material u-s-m-b-16">
                        <h6>Email</h6>
                        <span>tbiu@wmsu.edu.ph</span>
                    </div>
                    <div class="contact-material u-s-m-b-16">
                        <h6>Telephone</h6>
                        <span>(062) 993 1314</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="u-s-p-t-80">
        <div id="map"></div>
    </div>
</div>
<!-- Contact-Page /- -->
@endsection