<?php use App\Models\Product; 
use App\Models\Currency; 
?>
@extends('front.layout.layout')
@section('content')
<!-- Page Introduction Wrapper -->
<div class="page-style-a">
    <div class="container">
        <div class="page-intro">
            <h2>Cart</h2>
            <ul class="bread-crumb">
                <li class="has-separator">
                    <i class="ion ion-md-home"></i>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li class="is-marked">
                    <a href="#">Thanks</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Page Introduction Wrapper /- -->
<!-- Cart-Page -->
<div class="page-cart u-s-p-t-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-12" align="center">
                <h3>YOUR ORDER HAS BEEN PLACED SUCCESSFULLY</h3>
                <p>
                    Your order number is {{ Session::get('order_id') }} and Grand total is
                    @if(isset($_GET['cy'])&&$_GET['cy']!="PHP")
                        @php 
                            $getCurrency = Currency::where('currency_code',$_GET['cy'])->first()->toArray();
                        @endphp
                        {{$_GET['cy']}} {{ round(Session::get('grand_total')/$getCurrency['exchange_rate'],2) }}
                    @else
                        PHP {{ Session::get('grand_total') }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
<!-- Cart-Page /- -->
@endsection

<?php
    Session::forget('grand_total');
    Session::forget('order_id');
    Session::forget('couponCode');
    Session::forget('couponAmount');
?>