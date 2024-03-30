<?php use App\Models\Product; 
use App\Models\Currency; 
?>
@extends('front.layout.layout')
@section('content')
<!-- Page Introduction Wrapper -->
<div class="page-style-a">
    <div class="container">
        <div class="page-intro">
            <h2>Checkout</h2>
            <ul class="bread-crumb">
                <li class="has-separator">
                    <i class="ion ion-md-home"></i>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li class="is-marked">
                    <a href="checkout.html">Checkout</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Page Introduction Wrapper /- -->
<!-- Checkout-Page -->
<div class="page-checkout u-s-p-t-80">
    <div class="container">
        @if(Session::has('error_message'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error: </strong> <?php echo Session::get('error_message'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        @endif
        
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="row">
                        <!-- Billing-&-Shipping-Details -->
                        <div class="col-lg-6" id="deliveryAddresses">
                            @include('front.products.delivery_addresses')
                        </div>
                        <!-- Billing-&-Shipping-Details /- -->
                        <!-- Checkout -->
                        <div class="col-lg-6">
                            <form name="checkoutForm" id="checkoutForm" action="{{ url('/checkout') }}" method="post">@csrf

                                @if(count($deliveryAddresses)>0)
                                    <h4 class="section-h4">Delivery Addresses</h4>
                                    @foreach($deliveryAddresses as $address)
                                        <div class="control-group" style="float:left; margin-right:5px;"><input type="radio" id="address{{ $address['id'] }}" name="address_id" value="{{ $address['id'] }}" shipping_charges="{{ $address['shipping_charges'] }}" total_price="{{ $total_price }}" coupon_amount="{{ Session::get('couponAmount') }}" codpincodeCount="{{ $address['codpincodeCount'] }}" prepaidpincodeCount="{{ $address['prepaidpincodeCount'] }}" checked></div>
                                        <div><label class="control-label">{{ $address['name'] }}, {{ $address['address'] }}, {{ $address['city'] }}, {{ $address['barangay'] }}, {{ $address['country'] }} ({{ $address['mobile'] }}) </label>
                                            <a style="float: right; margin-left: 10px;" href="javascript:;" data-addressid="{{ $address['id'] }}" class="removeAddress">Remove</a>
                                            <a style="float: right;" href="javascript:;" data-addressid="{{ $address['id'] }}" class="editAddress">Edit</a>
                                            
                                        </div>
                                    @endforeach<br>
                                @endif
                                
                                <h4 class="section-h4">Your Order</h4>
                                <div class="order-table">
                                    <table class="u-s-m-b-13">  
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $total_price = 0 @endphp
                                            @foreach($getCartItems as $item)
                                            <?php $getDiscountAttributePrice = Product::getDiscountAttributePrice($item['product_id'],$item['size']); ?>
                                            <tr>
                                                <td>
                                                    <a href="{{ url('product/'.$item['product_id'])}}"><img width="50" src="{{ asset('front/images/product_images/small/'.$item['product']['product_image']) }}" alt="Product">
                                                    <h6 class="order-h6">{{ $item['product']['product_name'] }}<br>{{ $item['size'] }}/{{ $item['product']['product_color'] }}</h6></a>
                                                    <span class="order-span-quantity">x {{ $item['quantity'] }}</span>
                                                </td>
                                                <td>
                                                    <h6 class="order-h6">
                                                        @if(isset($_GET['cy']) && $_GET['cy'] != "PHP")
                                                            @php $getCurrency = Currency::where('currency_code',$_GET['cy'])->first()->toArray(); @endphp
                                                            {{ $_GET['cy'] }} {{ number_format(round($getDiscountAttributePrice['final_price'] * $item['quantity'] / $getCurrency['exchange_rate'], 2), 2) }}
                                                        @else
                                                            PHP {{ number_format($getDiscountAttributePrice['final_price'] * $item['quantity'], 2) }}
                                                        @endif
                                                    </h6>
                                                </td>
                                            </tr>
                                            @php $total_price = $total_price + ($getDiscountAttributePrice['final_price'] * $item['quantity']) @endphp
                                            @endforeach
                                            @if(isset($_GET['cy']) && $_GET['cy'] != "PHP")
                                                @php $total_price = number_format(round($total_price / $getCurrency['exchange_rate'], 2), 2) @endphp
                                            @endif
                                            <tr>
                                                <td>
                                                    <h3 class="order-h3">Subtotal</h3>
                                                </td>
                                                <td>
                                                    <h3 class="order-h3">
                                                        @if(isset($_GET['cy']) && $_GET['cy'] != "PHP")
                                                            {{ $_GET['cy'] }} {{ number_format($total_price, 2) }}
                                                        @else
                                                            PHP {{ number_format($total_price, 2) }}
                                                        @endif
                                                    </h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h6 class="order-h6">Shipping Charges</h6>
                                                </td>
                                                <td>
                                                    <h6 class="order-h6"><span class="shipping_charges">PHP </span></h6>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h6 class="order-h6">Coupon Discount</h6>
                                                </td>
                                                <td>
                                                    <h6 class="order-h6">
                                                        @if(isset($_GET['cy']) && $_GET['cy'] != "PHP")
                                                            @if(Session::has('couponAmount'))
                                                                <span class="couponAmount">
                                                                    {{ $_GET['cy'] }} {{ number_format(round(Session::get('couponAmount') / $getCurrency['exchange_rate'], 2), 2) }}
                                                                </span>
                                                            @else
                                                                {{ $_GET['cy'] }} 0
                                                            @endif
                                                        @else
                                                            @if(Session::has('couponAmount'))
                                                                <span class="couponAmount">PHP {{ Session::get('couponAmount') }}</span>
                                                            @else
                                                                PHP 0
                                                            @endif
                                                        @endif
                                                    </h6>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h3 class="order-h3">Grand Total</h3>
                                                </td>
                                                <td>
                                                    <h3 class="order-h3">
                                                        <strong class="grand_total">
                                                            @if(isset($_GET['cy']) && $_GET['cy'] != "PHP")
                                                                {{ $_GET['cy'] }} {{ number_format(round($total_price - Session::get('couponAmount') / $getCurrency['exchange_rate'], 2), 2) }}
                                                            @else
                                                                PHP {{ number_format($total_price - Session::get('couponAmount'), 2) }}
                                                            @endif
                                                        </strong>
                                                    </h3>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="u-s-m-b-13 codMethod">
                                        <input type="radio" class="radio-box" name="payment_gateway" id="cash-on-delivery" value="COD">
                                        <label class="label-text" for="cash-on-delivery">Cash on Delivery</label>
                                    </div>
                                    <div class="u-s-m-b-13 prepaidMethod">
                                        <input type="radio" class="radio-box" name="payment_gateway" id="online-payment" value="ONLINE">
                                        <label class="label-text" for="online-payment">Online Payment</label>
                                    </div>
                                    <div class="u-s-m-b-13">
                                        <input type="checkbox" class="check-box" id="accept" name="accept" value="Yes" title="Please agree to T&C">
                                        <label class="label-text no-color" for="accept">I’ve read and accept the
                                            <a href="terms-and-conditions.html" class="u-c-brand">terms & conditions</a>
                                        </label>
                                    </div>
                                    <button type="submit" id="placeOrder" class="button button-outline-secondary">Place Order</button>
                                </div>      

                            </form>
                        </div>
                        <!-- Checkout /- -->
                    </div>
                </div>
            </div>
    </div>
</div>
<!-- Checkout-Page /- -->
@endsection