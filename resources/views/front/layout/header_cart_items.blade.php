<?php 
use App\Models\Product; 
$getCartItems = getCartItems();
?>
<!-- Mini Cart -->
<div class="mini-cart-wrapper">
    <div class="mini-cart">
        <div class="mini-cart-header">
            YOUR CART
            <button type="button" class="button ion ion-md-close" id="mini-cart-close"></button>
        </div>
        <ul class="mini-cart-list">
            @php $total_price = 0 @endphp
            @foreach($getCartItems as $item)
            <?php $getDiscountAttributePrice = Product::getDiscountAttributePrice($item['product_id'],$item['size']);
            ?>
            <li class="clearfix">
                <a href="{{ url('product/'.$item['product_id'])}}">
                    <img src="{{ asset('front/images/product_images/small/'.$item['product']['product_image']) }}" alt="Product">
                    <span class="mini-item-name">{{ $item['product']['product_name'] }}</span>
                    <span class="mini-item-price">PHP {{ $getDiscountAttributePrice['final_price'] }}</span>
                    <span class="mini-item-quantity"> x {{ $item['quantity'] }} </span>
                </a>
            </li>
            @php $total_price = $total_price + ($getDiscountAttributePrice['final_price'] * $item['quantity']) @endphp
            @endforeach
        </ul>
        <div class="mini-shop-total clearfix">
            <span class="mini-total-heading float-left">Total:</span>
            <span class="mini-total-price float-right">PHP {{ number_format($total_price, 2, '.', ',') }}</span>
        </div>
        <div class="mini-action-anchors">
            <a href="{{url('cart')}}" class="cart-anchor">View Cart</a>
            <a href="{{url('checkout')}}" class="checkout-anchor">Checkout</a>
        </div>
    </div>
</div>

   