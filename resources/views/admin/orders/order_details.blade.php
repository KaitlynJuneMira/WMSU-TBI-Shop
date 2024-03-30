<?php 
use App\Models\Product; 
use App\Models\OrdersLog; 
use App\Models\Vendor; 
use App\Models\Coupon; 
?>
@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
      @if(Session::has('success_message'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success: </strong> {{ Session::get('success_message')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        @endif
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Order Details</h3>
                        <a href="javascript:history.back()" class="back-link">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>                    
                    </div>
                    
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Order Details</h4>
                  
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Order ID: </label>
                      <label>#{{ $orderDetails['id'] }}</label>
                    </div>
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Order Date: </label>
                      <label>{{ date('Y-m-d h:i:s', strtotime($orderDetails['created_at'])); }}</label>
                    </div>
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Order Status: </label>
                      <label>{{ $orderDetails['order_status'] }}</label>
                    </div>
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Order Total: </label>
                      <label>PHP {{ number_format($orderDetails['grand_total'], 2) }}</label>

                    </div>
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Shipping Charges: </label>
                      <label>PHP {{ $orderDetails['shipping_charges'] }}</label>
                    </div>
                    @if(!empty($orderDetails['coupon_code']))
                      <div class="form-group" style="height: 15px;">
                        <label style="font-weight: 550;">Coupon Code: </label>
                        <label>{{ $orderDetails['coupon_code'] }}</label>
                      </div>
                      <div class="form-group" style="height: 15px;">
                        <label style="font-weight: 550;">Coupon Amount: </label>
                        <label>PHP {{ $orderDetails['coupon_amount'] }}</label>
                      </div>
                    @endif
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Payment Method: </label>
                      <label>{{ $orderDetails['payment_method'] }}</label>
                    </div>
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Payment Gateway: </label>
                      <label>{{ $orderDetails['payment_gateway'] }}</label>
                    </div>
                  
                </div>
              </div>
            </div>
            
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Customer Details</h4>
                  
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Name: </label>
                      <label>{{ $userDetails['name'] }}</label>
                    </div>
                    @if(!empty($userDetails['address']))
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Address: </label>
                      <label>{{ $userDetails['address'] }}</label>
                    </div>
                    @endif
                    @if(!empty($userDetails['city']))
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">City: </label>
                      <label>{{ $userDetails['city'] }}</label>
                    </div>
                    @endif
                    @if(!empty($userDetails['barangay']))
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Barangay: </label>
                      <label>{{ $userDetails['barangay'] }}</label>
                    </div>
                    @endif
                    @if(!empty($userDetails['country']))
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Country: </label>
                      <label>{{ $userDetails['country'] }}</label>
                    </div>
                    @endif
                    @if(!empty($userDetails['pincode']))
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Zipcode: </label>
                      <label>{{ $userDetails['pincode'] }}</label>
                    </div>
                    @endif
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Mobile: </label>
                      <label>{{ $userDetails['mobile'] }}</label>
                    </div>
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Email: </label>
                      <label>{{ $userDetails['email'] }}</label>
                    </div>
                   
                   
                </div>
              </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Delivery Address</h4>
                  
                  <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Name: </label>
                      <label>{{ $orderDetails['name'] }}</label>
                    </div>
                    @if(!empty($orderDetails['address']))
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Address: </label>
                      <label>{{ $orderDetails['address'] }}</label>
                    </div>
                    @endif
                    @if(!empty($orderDetails['city']))
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">City: </label>
                      <label>{{ $orderDetails['city'] }}</label>
                    </div>
                    @endif
                    @if(!empty($orderDetails['state']))
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">State: </label>
                      <label>{{ $orderDetails['state'] }}</label>
                    </div>
                    @endif
                    @if(!empty($orderDetails['country']))
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Country: </label>
                      <label>{{ $orderDetails['country'] }}</label>
                    </div>
                    @endif
                    @if(!empty($orderDetails['pincode']))
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Zipcode: </label>
                      <label>{{ $orderDetails['pincode'] }}</label>
                    </div>
                    @endif
                    <div class="form-group" style="height: 15px;">
                      <label style="font-weight: 550;">Mobile: </label>
                      <label>{{ $orderDetails['mobile'] }}</label>
                    </div>
                   
                </div>
              </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Update Order Status</h4>
                  @if(Auth::guard('admin')->user()->type!="vendor")
                  <form action="{{ url('admin/update-order-status') }}" method="post" >@csrf
                    <input type="hidden" name="order_id" value="{{ $orderDetails['id'] }}">
                    <select name="order_status" id="order_status" required="">
                      <option value="" selected="">Select</option>
                      @foreach($orderStatuses as $status)
                        <option value="{{ $status['name'] }}" @if(!empty($orderDetails['order_status']) && $orderDetails['order_status']==$status['name']) selected="" @endif>{{ $status['name'] }}</option>
                      @endforeach
                    </select>
                    <input type="text" name="courier_name" id="courier_name" placeholder="Courier Name">
                    <input type="text" name="tracking_number" id="tracking_number" placeholder="Tracking Number">
                    <button type="submit">Update</button>
                   </form>
                   <br>
                   @foreach($orderLog as $key => $log)
                   <?php //echo "<pre>"; print_r($log['orders_products'][$key]['product_code']); die; ?>
                    <strong>{{ $log['order_status'] }}</strong>
                    
                      @if(isset($log['order_item_id'])&&$log['order_item_id']>0)
                        @php $getItemDetails = OrdersLog::getItemDetails($log['order_item_id']) @endphp
                        - for item {{ $getItemDetails['product_code'] }}
                        @if(!empty($getItemDetails['courier_name']))
                          <br><span>Courier Name: {{ $getItemDetails['courier_name'] }}</span>
                        @endif
                        @if(!empty($getItemDetails['tracking_number']))
                          <br><span>Tracking Number: {{ $getItemDetails['tracking_number'] }}</span>
                        @endif
                      
                      @endif
                    
                    <br>{{ date('Y-m-d h:i:s', strtotime($log['created_at'])); }}<br>
                    <hr>
                   @endforeach
                   @else
                    This feature is restricted.
                   @endif

                </div>
              </div>
            </div>

            @foreach($orderDetails['orders_products'] as $product)
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Ordered Products</h4>
                
                @php 
                    $getProductImage = Product::getProductImage($product['product_id']);
                    $imageSrc = asset('front/images/product_images/small/'.$getProductImage);
                @endphp
                <div class="form-group">
                    <label style="font-weight: 550;">Product Image:</label>
                    <a target="_blank" href="{{ url('product/'.$product['product_id']) }}"><img src="{{ $imageSrc }}" style="max-width: 200px;"></a>
                </div>
                               <div class="form-group" style="height: 15px;">
                    <label style="font-weight: 550;">Code:</label>
                    <label>{{ $product['product_code'] }}</label>
                </div>
                <div class="form-group" style="height: 15px;">
                    <label style="font-weight: 550;">Name:</label>
                    <label>{{ $product['product_name'] }}</label>
                </div>
                <div class="form-group" style="height: 15px;">
                    <label style="font-weight: 550;">Size:</label>
                    <label>{{ $product['product_size'] }}</label>
                </div>
                <div class="form-group" style="height: 15px;">
                    <label style="font-weight: 550;">Color:</label>
                    <label>{{ $product['product_color'] }}</label>
                </div>
                <div class="form-group" style="height: 15px;">
                    <label style="font-weight: 550;">Unit Price:</label>
                    <label>{{ $product['product_price'] }}</label>
                </div>
                <div class="form-group" style="height: 15px;">
                    <label style="font-weight: 550;">Product Qty:</label>
                    <label>{{ $product['product_qty'] }}</label>
                </div>
                <div class="form-group" style="height: 15px;">
    <label style="font-weight: 550;">Total Price:</label>
    <label>
        @php
            $total_price = $product['product_price'] * $product['product_qty'];
            if($product['vendor_id'] > 0) {
                if($orderDetails['coupon_amount'] > 0) {
                    $couponDetails = Coupon::couponDetails($orderDetails['coupon_code']);
                    if($couponDetails['vendor_id'] > 0) {
                        $total_price -= $item_discount;
                    }
                }
            }
            echo number_format($total_price, 2);
        @endphp
    </label>
</div>
@if(Auth::guard('admin')->user()->type != "vendor")
<div class="form-group" style="height: 15px;">
        <label style="font-weight: 550;">Product by:</label>
        <label>
            @if($product['vendor_id'] > 0)
                <a target="_blank" href="/admin/view-vendor-details/{{ $product['admin_id'] }}">Vendor</a>
            @else
                Admin
            @endif
        </label>
    </div>
@endif

                   <div class="form-group" style="height: 15px;">
                    <label style="font-weight: 550;">Commission:</label>
                    <label>
                        @if($product['vendor_id']>0)
                            {{ $commission = round($total_price * $product['commission']/100,2) }}
                        @else
                            0
                        @endif
                    </label>
                </div>
                <div class="form-group" style="height: 15px;">
    <label style="font-weight: 550;">Final Amount:</label>
    <label>
        @if($product['vendor_id'] > 0)
            {{ number_format($total_price - $commission, 2) }}
        @else
            {{ number_format($total_price, 2) }}
        @endif
    </label>
</div>

                <div class="form-group">
                    <form action="{{ url('admin/update-order-item-status') }}" method="post" >
                        @csrf
                        <input type="hidden" name="order_item_id" value="{{ $product['id'] }}">
                        <div class="form-group">
                        <label style="font-weight: 550;">Order Status:</label>
                        <select name="order_item_status" id="order_item_status" required="">
                            <option value="">Select</option>
                            @foreach($orderItemStatuses as $status)
                                <option value="{{ $status['name'] }}" @if(!empty($product['item_status']) && $product['item_status']==$status['name']) selected="" @endif>{{ $status['name'] }}</option>
                            @endforeach
                        </select>
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 550;">Delivery Rider Name:</label>
                            <input style="width:200px;" type="text" name="item_courier_name" id="item_courier_name" placeholder="Courier Name" @if(!empty($product['courier_name'])) value="{{ $product['courier_name'] }}" @endif>
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 550;">Tracking Number:</label>
                            <input style="width:220px;" type="text" name="item_tracking_number" id="item_tracking_number" placeholder="Tracking Number" @if(!empty($product['tracking_number'])) value="{{ $product['tracking_number'] }}" @endif>
                        </div>
                        <div style="text-align: center;">
    <button style="width: 200px; margin: 0 auto; background-color: #007bff; color: white; border: none; padding: 10px 20px; font-size: 16px; cursor: pointer; border-radius: 5px;" type="submit">Update</button>
</div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

          </div>
        
    </div>
    <!-- content-wrapper ends -->
    <!-- partial:partials/_footer.html -->
    <!-- @include('admin.layout.footer') -->
    <!-- partial -->
</div>
@endsection