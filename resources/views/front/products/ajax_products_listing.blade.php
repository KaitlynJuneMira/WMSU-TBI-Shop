<?php 
use App\Models\Product; 
use App\Models\Currency; 
?>
<div class="row product-container grid-style">
	@foreach($categoryProducts as $product)
    <div class="product-item col-lg-4 col-md-6 col-sm-6">
        <div class="item">
            <div class="image-container">
                <a class="item-img-wrapper-link" href="{{ url('product/'.$product['id']) }}">
                	<?php $product_image_path = 'front/images/product_images/small/'.$product['product_image']; ?>
                	@if(!empty($product['product_image']) && file_exists($product_image_path))
                    	<img class="img-fluid" src="{{ asset($product_image_path) }}" alt="Product">
                    @else
                    	<img class="img-fluid" src="{{ asset('front/images/product_images/small/no-image.png') }}" alt="Product">
                    @endif
                </a>
                <!-- <div class="item-action-behaviors">
                    <a class="item-quick-look" data-toggle="modal" href="#quick-view">Quick Look</a>
                    <a class="item-mail" href="javascript:void(0)">Mail</a>
                    <a class="item-addwishlist" href="javascript:void(0)">Add to Wishlist</a>
                    <a class="item-addCart" href="javascript:void(0)">Add 1to Cart</a>
                </div> -->
            </div>
            <div class="item-content">
                <div class="what-product-is">
                    <ul class="bread-crumb">
                        <li class="has-separator">
                            <a href="shop-v1-root-category.html">{{ $product['product_code'] }}</a>
                        </li>
                        <li class="has-separator">
                            <a href="listing.html">{{ $product['product_color'] }}</a>
                        </li>
                        <li>
                            <a href="listing.html">{{ $product['brand']['name'] }}</a>
                        </li>
                    </ul>
                    <h6 class="item-title">
                        <a href="{{ url('product/'.$product['id']) }}">{{ $product['product_name'] }}</a>
                    </h6>
                    <div class="item-description">
                        <p>{{ $product['description'] }}
                        </p>
                    </div>
                </div>
                <?php $getDiscountPrice = Product::getDiscountPrice($product['id']); ?>
                @if(isset($_GET['cy'])&&$_GET['cy']!="PHP")
                    @php 
                        $getCurrency = Currency::where('currency_code',$_GET['cy'])->first()->toArray();
                    @endphp
                    @if($getDiscountPrice>0)
                    <div class="price-template">
                        <div class="item-new-price">
                            {{$_GET['cy']}} {{ round($getDiscountPrice/$getCurrency['exchange_rate'],2) }}
                        </div>
                        <div class="item-old-price">
                            {{$_GET['cy']}} {{ round($product['product_price']/$getCurrency['exchange_rate'],2) }}
                        </div>
                    </div>
                    @else
                    <div class="price-template">
                        <div class="item-new-price">
                            {{$_GET['cy']}} {{ round($product['product_price']/$getCurrency['exchange_rate'],2) }}
                        </div>
                    </div>
                    @endif
                @else
                    @if($getDiscountPrice>0)
                    <div class="price-template">
                        <div class="item-new-price">
                            PHP {{ $getDiscountPrice }}
                        </div>
                        <div class="item-old-price">
                            PHP {{ $product['product_price'] }}
                        </div>
                    </div>
                    @else
                    <div class="price-template">
                        <div class="item-new-price">
                            PHP {{ $product['product_price'] }}
                        </div>
                    </div>
                    @endif
                @endif
            </div>
            <?php $isProductNew = Product::isProductNew($product['id']); ?>
            @if($isProductNew=="Yes")
            <div class="tag new">
                <span>NEW</span>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>