<?php use App\Models\Product; ?>
@extends('front.layout.layout')
@section('content')
<!-- Page Introduction Wrapper -->
<div class="page-style-a">
    <div class="container">
        <div class="page-intro">
            <h2>Order #{{ $orderDetails['id'] }} Details</h2>
            <ul class="bread-crumb">
                <li class="has-separator">
                    <i class="ion ion-md-home"></i>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li class="is-marked">
                    <a href="{{ url('user/orders') }}">Orders</a>
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
            <table class="table table-striped table-borderless">
                <tr class="table-danger"><td colspan="2"><strong>Order Details</strong></td></tr>
                <tr><td>Order Date</td><td>{{ date('Y-m-d h:i:s', strtotime($orderDetails['created_at'])) }}</td></tr>
                <tr><td>Order Status</td><td>{{ $orderDetails['order_status']}}</td></tr>
                <tr><td>Order Total</td><td>PHP {{ $orderDetails['grand_total']}}</td></tr>
                <tr><td>Shipping Charges</td><td>PHP {{ $orderDetails['shipping_charges']}}</td></tr>
                @if($orderDetails['coupon_code']!="")
                <tr><td>Coupon Code</td><td>{{ $orderDetails['coupon_code']}}</td></tr>
                <tr><td>Coupon Amount</td><td>PHP {{ $orderDetails['coupon_amount']}}</td></tr>
                @endif
                @if($orderDetails['courier_name']!="")
                <tr><td>Courier Name</td><td>{{ $orderDetails['courier_name']}}</td></tr>
                <tr><td>Tracking Number</td><td>{{ $orderDetails['tracking_number']}}</td></tr>
                @endif
                <tr><td>Payment Method</td><td>{{ $orderDetails['payment_method']}}</td></tr>
            </table>
            <table class="table table-striped table-borderless">
    <tr class="table-danger">
        <th>Product Image</th>
        <th>Product Code</th>
        <th>Product Name</th>
        <th>Product Size</th>
        <th>Product Color</th>
        <th>Product Qty</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    @foreach($orderDetails['orders_products'] as $product)
        <tr>
            <td>
                @php $getProductImage = Product::getProductImage($product['product_id']) @endphp
                <a target="_blank" href="{{ url('product/'.$product['product_id']) }}"><img style="width:80px" src="{{ asset('front/images/product_images/small/'.$getProductImage) }}"></a>
            </td>
            <td>{{ $product['product_code'] }}</td>
            <td>{{ $product['product_name'] }}</td>
            <td>{{ $product['product_size'] }}</td>
            <td>{{ $product['product_color'] }}</td>
            <td>{{ $product['product_qty'] }}</td>
            <td>{{ $product['item_status'] }}</td>
            <td>
                @if($product['item_status']=="New" || $product['item_status']=="Pending" || $product['item_status']=="In Progress")
                <a href="{{ url('cancel-product/'.$product['id']) }}" id="cancel_product" class="btn btn-danger btn-sm">Cancel</a>
                @endif
                @if($product['item_status']=="Delivered")
                <a href="{{ url('replace-order/'.$product['id']) }}" id="replace_refund" class="btn btn-warning btn-sm">Return/Refund</a>
                @endif
            </td>
        </tr>
        @if($product['courier_name']!="")
        <tr><td colspan="6">Courier Name: {{ $product['courier_name']}}, Tracking Number: {{ $product['tracking_number']}} </td></tr>
        @endif
    @endforeach   
</table>

            <table class="table table-striped table-borderless">
                <tr class="table-danger"><td colspan="2"><strong>Delivery Address</strong></td></tr>
                <tr><td>Name</td><td>{{ $orderDetails['name']}}</td></tr>
                <tr><td>Address Details</td><td>{{ $orderDetails['address']}}</td></tr>
                <tr><td>City</td><td>{{ $orderDetails['city']}}</td></tr>
                <tr><td>Barangay</td><td>{{ $orderDetails['state']}}</td></tr>
                <tr><td>Province</td><td>{{ $orderDetails['country']}}</td></tr>
                <tr><td>Pincode</td><td>{{ $orderDetails['pincode']}}</td></tr>
                <tr><td>Mobile</td><td>{{ $orderDetails['mobile']}}</td></tr>
            </table>
            <a style="max-width: 150px; float: right; display: inline-block; background-color: red; color: white;"  @if($orderDetails['order_status']!="Cancelled" ) href="{{ url('cancel-order/'.$orderDetails['id']) }}" id="cancel_product" @endif class="btn btn-block btn-primary">Cancel Order</a>

        </div>
       

    </div>
</div>
<!-- Cart-Page /- -->
@endsection