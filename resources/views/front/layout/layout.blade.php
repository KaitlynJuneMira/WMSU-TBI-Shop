<!DOCTYPE html>
<html class="no-js" lang="en-US">

<head>
    <meta charset="UTF-8">
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="">
    <meta name="author" content="">
    <title>@if(!empty($meta_title)){{ $meta_title }} @else WMSU TBI Shop @endif</title>
    @if(!empty($meta_description))<meta name="description" content="{{ $meta_description }}">@endif
    
    @if(!empty($meta_keywords))<meta name="keywords" content="{{ $meta_keywords }}">@endif
    <!-- Standard Favicon -->
    <link rel="shortcut icon" href="{{ url('admin/images/tbilogo.png') }}" />
    <!-- Base Google Font for Web-app -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
    <!-- Google Fonts for Banners only -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,800" rel="stylesheet">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="{{ url('front/css/bootstrap.min.css') }}">
    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="{{ url('front/css/fontawesome.min.css') }}">
    <!-- Ion-Icons 4 -->
    <link rel="stylesheet" href="{{ url('front/css/ionicons.min.css') }}">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="{{ url('front/css/animate.min.css') }}">
    <!-- Owl-Carousel -->
    <link rel="stylesheet" href="{{ url('front/css/owl.carousel.min.css') }}">
    <!-- Jquery-Ui-Range-Slider -->
    <link rel="stylesheet" href="{{ url('front/css/jquery-ui-range-slider.min.css') }}">
    <!-- Utility -->
    <link rel="stylesheet" href="{{ url('front/css/utility.css') }}">
    <!-- Main -->
    <link rel="stylesheet" href="{{ url('front/css/bundle.css') }}">
    <!-- Zoom -->
    <link rel="stylesheet" href="{{ url('front/css/easyzoom.css') }}">
    <!-- Custom -->
    <link rel="stylesheet" href="{{ url('front/css/custom.css') }}">
    <!-- toastr -->
    <link rel="stylesheet" type="text/css" href="{{ url('front/plugins/toastr/toastr.css') }}">

    <!-- {{-- sweetalert2 --}} -->
    <link rel="stylesheet" type="text/css" href="{{ url('front/plugins/sweetalert2/sweetalert2.css') }}">
</head>

<body>

    <!-- Google Translate Script -->
   <!--<div id="google_translate_element"></div>
   <script type="text/javascript">
      function googleTranslateElementInit() {
         new google.translate.TranslateElement({
            pageLanguage: 'en', layout: 
            google.translate.TranslateElement.InlineLayout.HORIZONTAL, autoDisplay: 
            false, includedLanguages: 'en,fr,de,es,hi,ur', gaTrack: true, gaId: 'AIzaSyDdMXtJJuAjvJdkVDNsSXVLmCSpwfH1GeU'
            }, 'google_translate_element');
      }
   </script>
   <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<div class="loader">
   <img src="{{ asset('front/images/loaders/loader.gif') }}" alt="loading..." />
</div>
    -->


<!-- app -->
<div id="app">
    @include('front.layout.header')
    @yield('content')
    @include('front.layout..footer')
    @include('front.layout.modals')
</div>
<!-- app /- -->
<!--[if lte IE 9]>
<div class="app-issue">
    <div class="vertical-center">
        <div class="text-center">
            <h1>You are using an outdated browser.</h1>
            <span>This web app is not compatible with following browser. Please upgrade your browser to improve your security and experience.</span>
        </div>
    </div>
</div>
<style> #app {
    display: none;
} </style>
<![endif]-->
<!-- NoScript -->
<noscript>
    <div class="app-issue">
        <div class="vertical-center">
            <div class="text-center">
                <h1>JavaScript is disabled in your browser.</h1>
                <span>Please enable JavaScript in your browser or upgrade to a JavaScript-capable browser.</span>
            </div>
        </div>
    </div>
    <style>
    #app {
        display: none;
    }
    </style>
</noscript>
<!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. -->
<script>
window.ga = function() {
    ga.q.push(arguments)
};
ga.q = [];
ga.l = +new Date;
ga('create', 'UA-XXXXX-Y', 'auto');
ga('send', 'pageview')
</script>
<script src="https://www.google-analytics.com/analytics.js" async defer></script>
<!-- Modernizr-JS -->
<script type="text/javascript" src="{{ url('front/js/vendor/modernizr-custom.min.js') }}"></script>
<!-- NProgress -->
<script type="text/javascript" src="{{ url('front/js/nprogress.min.js') }}"></script>
<!-- jQuery -->
<script type="text/javascript" src="{{ url('front/js/jquery.min.js') }}"></script>
<!-- Bootstrap JS -->
<script type="text/javascript" src="{{ url('front/js/bootstrap.min.js') }}"></script>
<!-- Popper -->
<script type="text/javascript" src="{{ url('front/js/popper.min.js') }}"></script>
<!-- ScrollUp -->
<script type="text/javascript" src="{{ url('front/js/jquery.scrollUp.min.js') }}"></script>
<!-- Elevate Zoom -->
<script type="text/javascript" src="{{ url('front/js/jquery.elevatezoom.min.js') }}"></script>
<!-- jquery-ui-range-slider -->
<script type="text/javascript" src="{{ url('front/js/jquery-ui.range-slider.min.js') }}"></script>
<!-- jQuery Slim-Scroll -->
<script type="text/javascript" src="{{ url('front/js/jquery.slimscroll.min.js') }}"></script>
<!-- jQuery Resize-Select -->
<script type="text/javascript" src="{{ url('front/js/jquery.resize-select.min.js') }}"></script>
<!-- jQuery Custom Mega Menu -->
<script type="text/javascript" src="{{ url('front/js/jquery.custom-megamenu.min.js') }}"></script>
<!-- jQuery Countdown -->
<script type="text/javascript" src="{{ url('front/js/jquery.custom-countdown.min.js') }}"></script>
<!-- Owl Carousel -->
<script type="text/javascript" src="{{ url('front/js/owl.carousel.min.js') }}"></script>
<!-- Main -->
<script type="text/javascript" src="{{ url('front/js/app.js') }}"></script>
<!-- Custom -->
<script type="text/javascript" src="{{ url('front/js/custom.js') }}"></script>
<!-- Zoom -->
<script type="text/javascript" src="{{ url('front/js/easyzoom.js') }}"></script>
<script>
        // Instantiate EasyZoom instances
        var $easyzoom = $('.easyzoom').easyZoom();

        // Setup thumbnails example
        var api1 = $easyzoom.filter('.easyzoom--with-thumbnails').data('easyZoom');

        $('.thumbnails').on('click', 'a', function(e) {
            var $this = $(this);

            e.preventDefault();

            // Use EasyZoom's `swap` method
            api1.swap($this.data('standard'), $this.attr('href'));
        });

        // Setup toggles example
        var api2 = $easyzoom.filter('.easyzoom--with-toggle').data('easyZoom');

        $('.toggle').on('click', function() {
            var $this = $(this);

            if ($this.data("active") === true) {
                $this.text("Switch on").data("active", false);
                api2.teardown();
            } else {
                $this.text("Switch off").data("active", true);
                api2._init();
            }
        });
    </script>

    <script type="text/javascript" src="{{ url('front/plugins/toastr/toastr.min.js') }}"></script>
    <!-- {{-- sweetalert2 --}} -->
    <script src="{{ url('front/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(document).on("click", "#cancel_product", function(e){
            e.preventDefault();
            var link = $(this).attr("href");

            Swal.fire({
                title: "Are you sure you want to Cancel this?",
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = link;
                }
            });
        });
    </script>

    <script>
        @if(Session::has('message'))
        var type="{{Session::get('alert-type','info')}}"
        switch(type){
            case 'info':
                toastr.info("{{ Session::get('message') }}");
                break;
            case 'success':
                toastr.success("{{ Session::get('message') }}");
                break;
            case 'warning':
                toastr.warning("{{ Session::get('message') }}");
                break;
            case 'error':
                toastr.error("{{ Session::get('message') }}");
                break;
                }
        @endif
    </script>
@include('front.layout.scripts')

</body>
</html>
