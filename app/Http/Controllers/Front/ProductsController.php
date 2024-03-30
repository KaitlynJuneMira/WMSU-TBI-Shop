<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductsFilter;
use App\Models\ProductsAttribute;
use App\Models\Vendor;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\User;
use App\Models\DeliveryAddress;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\Sms;
use App\Models\ShippingCharge;
use App\Models\Currency;
use App\Models\Barangay;
use App\Models\Rating;
use Session;
use DB;
use Auth;

class ProductsController extends Controller
{
    // Product filtering
    public function listing(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $url = $data['url'];
            $_GET['sort'] = $data['sort'];
            $categoryCount = Category::where(['url'=>$url,'status'=>1])->count();
            if($categoryCount>0){
                // Get category details
                $categoryDetails = Category::categoryDetails($url);
                $categoryProducts = Product::with('brand')->whereIn('category_id',$categoryDetails['catIds'])->where('status',1);
                // Checking for dynamic filters
                $productFilters = ProductsFilter::productFilters();
                foreach ($productFilters as $key => $filter) {
                    // If filter is selected
                    if(isset($filter['filter_column']) && isset($data[$filter['filter_column']]) && !empty($filter['filter_column']) && !empty($data[$filter['filter_column']])){
                        $categoryProducts->whereIn($filter['filter_column'],$data[$filter['filter_column']]);
                    }
                }
                // Checking for sort
                if(isset($_GET['sort']) && !empty($_GET['sort'])){
                    if($_GET['sort']=="product_latest"){
                        $categoryProducts->orderby('products.id','Desc');
                    }else if($_GET['sort']=="price_lowest"){
                        $categoryProducts->orderby('products.product_price','Asc');
                    }else if($_GET['sort']=="price_highest"){
                        $categoryProducts->orderby('products.product_price','Desc');
                    }else if($_GET['sort']=="name_z_a"){
                        $categoryProducts->orderby('products.product_name','Desc');
                    }else if($_GET['sort']=="name_a_z"){
                        $categoryProducts->orderby('products.product_name','Asc');
                    }
                }
                // Checking for size
                if(isset($data['size']) && !empty($data['size'])){
                    $productIds = ProductsAttribute::select('product_id')->whereIn('size',$data['size'])->pluck('product_id')->toArray();
                    $categoryProducts->whereIn('products.id',$productIds);
                }
                // Checking for color
                if(isset($data['color']) && !empty($data['color'])){
                    $productIds = Product::select('id')->whereIn('product_color',$data['color'])->pluck('id')->toArray();
                    $categoryProducts->whereIn('products.id',$productIds);
                }
                // Checking for price
                $productIds = array();
                if(isset($data['price']) && !empty($data['price'])){
                    foreach ($data['price'] as $key => $price) {
                        $priceArr = explode("-",$price);
                        if(isset($priceArr[0]) && isset($priceArr[1])){
                            $productIds[] = Product::select('id')->whereBetween('product_price',[$priceArr[0],$priceArr[1]])->pluck('id')->toArray();
                        }
                    }
                    $productIds = array_unique(array_flatten($productIds));
                    $categoryProducts->whereIn('products.id',$productIds);
                }
                // Checking for brand
                if(isset($data['brand']) && !empty($data['brand'])){
                    $productIds = Product::select('id')->whereIn('brand_id',$data['brand'])->pluck('id')->toArray();
                    $categoryProducts->whereIn('products.id',$productIds);
                }

                $categoryProducts = $categoryProducts->paginate(30);
                /*dd($categoryDetails);
                /*echo "Category exists"; die;*/
                $meta_title = $categoryDetails['categoryDetails']['meta_title'];
                $meta_keywords = $categoryDetails['categoryDetails']['meta_keywords'];
                $meta_description = $categoryDetails['categoryDetails']['meta_description'];
                return view('front.products.ajax_products_listing')->with(compact('categoryDetails','categoryProducts','url','meta_title','meta_description','meta_keywords'));
            }else{
                abort(404);
            }

        }else{
            // Search products
            if(isset($_REQUEST['search']) && !empty($_REQUEST['search'])){
                if($_REQUEST['search']=="new-arrivals"){
                    $search_product = $_REQUEST['search'];
                    $categoryDetails['breadcrumbs'] = "New Arrival Products";
                    $categoryDetails['categoryDetails']['category_name'] = "New Arrival Products";
                    $categoryDetails['categoryDetails']['description'] = "New Arrival Products";
                    $categoryProducts = Product::select('products.id','products.section_id','products.category_id','products.brand_id','products.vendor_id','products.product_name','products.product_code','products.product_color','products.product_price','products.product_discount','products.product_image','products.description')->with('brand')->join('categories','categories.id','=','products.category_id')->where('products.status',1)->orderby('id','Desc');   
                }else if($_REQUEST['search']=="best-sellers"){
                    $search_product = $_REQUEST['search'];
                    $categoryDetails['breadcrumbs'] = "Best Sellers Products";
                    $categoryDetails['categoryDetails']['category_name'] = "Best Sellers Products";
                    $categoryDetails['categoryDetails']['description'] = "Best Sellers Products";
                    $categoryProducts = Product::select('products.id','products.section_id','products.category_id','products.brand_id','products.vendor_id','products.product_name','products.product_code','products.product_color','products.product_price','products.product_discount','products.product_image','products.description')->with('brand')->join('categories','categories.id','=','products.category_id')->where('products.status',1)->where('products.is_bestseller','Yes');   
                }else if($_REQUEST['search']=="featured"){
                    $search_product = $_REQUEST['search'];
                    $categoryDetails['breadcrumbs'] = "Featured Products";
                    $categoryDetails['categoryDetails']['category_name'] = "Featured Products";
                    $categoryDetails['categoryDetails']['description'] = "Featured Products";
                    $categoryProducts = Product::select('products.id','products.section_id','products.category_id','products.brand_id','products.vendor_id','products.product_name','products.product_code','products.product_color','products.product_price','products.product_discount','products.product_image','products.description')->with('brand')->join('categories','categories.id','=','products.category_id')->where('products.status',1)->where('products.is_featured','Yes');   
                }else if($_REQUEST['search']=="discounted"){
                    $search_product = $_REQUEST['search'];
                    $categoryDetails['breadcrumbs'] = "Discounted Products";
                    $categoryDetails['categoryDetails']['category_name'] = "Discounted Products";
                    $categoryDetails['categoryDetails']['description'] = "Discounted Products";
                    $categoryProducts = Product::select('products.id','products.section_id','products.category_id','products.brand_id','products.vendor_id','products.product_name','products.product_code','products.product_color','products.product_price','products.product_discount','products.product_image','products.description')->with('brand')->join('categories','categories.id','=','products.category_id')->where('products.status',1)->where('products.product_discount','>',0);   
                }else{
                    $search_product = $_REQUEST['search'];
                    $categoryDetails['breadcrumbs'] = $search_product;
                    $categoryDetails['categoryDetails']['category_name'] = $search_product;
                    $categoryDetails['categoryDetails']['description'] = "Search Product for ". $search_product;
                    $categoryProducts = Product::select('products.id','products.section_id','products.category_id','products.brand_id','products.vendor_id','products.product_name','products.product_code','products.product_color','products.product_price','products.product_discount','products.product_image','products.description')->with('brand')->join('categories','categories.id','=','products.category_id')->where(function($query)use($search_product){
                            $query->where('products.product_name','like','%'.$search_product.'%')
                            ->orWhere('products.product_code','like','%'.$search_product.'%')
                            ->orWhere('products.product_color','like','%'.$search_product.'%')
                            ->orWhere('products.description','like','%'.$search_product.'%')
                            ->orWhere('categories.category_name','like','%'.$search_product.'%');
                    })->where('products.status',1);
                }

                if(isset($_REQUEST['section_id']) && !empty($_REQUEST['section_id'])){
                    $categoryProducts = $categoryProducts->where('products.section_id',$_REQUEST['section_id']); 
                }
                $categoryProducts = $categoryProducts->get();
                /*dd($categoryProducts);*/
                return view('front.products.listing')->with(compact('categoryDetails','categoryProducts'));
            }else{
                $url = Route::getFacadeRoot()->current()->uri();
                $categoryCount = Category::where(['url'=>$url,'status'=>1])->count();
                if($categoryCount>0){
                    // Get category details
                    $categoryDetails = Category::categoryDetails($url);
                    $categoryProducts = Product::with('brand')->whereIn('category_id',$categoryDetails['catIds'])->where('status',1);

                    // Checking for sort
                    if(isset($_GET['sort']) && !empty($_GET['sort'])){
                        if($_GET['sort']=="product_latest"){
                            $categoryProducts->orderby('products.id','Desc');
                        }else if($_GET['sort']=="price_lowest"){
                            $categoryProducts->orderby('products.product_price','Asc');
                        }else if($_GET['sort']=="price_highest"){
                            $categoryProducts->orderby('products.product_price','Desc');
                        }else if($_GET['sort']=="name_z_a"){
                            $categoryProducts->orderby('products.product_name','Desc');
                        }else if($_GET['sort']=="name_a_z"){
                            $categoryProducts->orderby('products.product_name','Asc');
                        }
                    }
                    $categoryProducts = $categoryProducts->paginate(30);
                    /*dd($categoryDetails);*/
                    /*echo "Category exists"; die;*/
                    $meta_title = $categoryDetails['categoryDetails']['meta_title'];
                    $meta_keywords = $categoryDetails['categoryDetails']['meta_keywords'];
                    $meta_description = $categoryDetails['categoryDetails']['meta_description'];
                    return view('front.products.listing')->with(compact('categoryDetails','categoryProducts','url','meta_title','meta_description','meta_keywords'));
                }else{
                    abort(404);
                }
            }
        }
    }

    public function vendorListing($vendorid){
        // Get vendor shop name
        $getVendorShop = Vendor::getVendorShop($vendorid);
        // Get vendor products
        $vendorProducts = Product::with('brand')->where('vendor_id',$vendorid)->where('status',1);
        $vendorProducts = $vendorProducts->paginate(30);
        return view('front.products.vendor_listing')->with(compact('getVendorShop','vendorProducts'));
    }

    public function indexdetail($id){
        $productDetails = Product::with(['section','category','brand','attributes'=>function($query){
            $query->where('stock','>',0)->where('status',1);
        },'images','vendor'])->find($id)->toArray();
        $categoryDetails = Category::categoryDetails($productDetails['category']['url']);
        // Get similar products
        $similarProducts = Product::with('brand')->where('category_id',$productDetails['category']['id'])->where('id','!=',$id)->limit(4)->inRandomOrder()->get()->toArray();
        /*dd($similarProducts);*/
        // Set Session for recently viewed products
        if(empty(Session::get('session_id'))){
            $session_id = md5(uniqid(rand(), true));
        }else{
            $session_id = Session::get('session_id');
        }

        Session::put('session_id',$session_id);

        // Insert product in table if not already exists
        $countRecentlyViewedProducts = DB::table('recently_viewed_products')->where(['product_id'=>$id,'session_id'=>$session_id])->count();
        if($countRecentlyViewedProducts==0){
            DB::table('recently_viewed_products')->insert(['product_id'=>$id,'session_id'=>$session_id]);
        }

        // Get recently viewed products IDs
        $recentProductsIds = DB::table('recently_viewed_products')->select('product_id')->where('product_id','!=',$id)->where('session_id',$session_id)->inRandomOrder()->get()->take(4)->pluck('product_id');
        /*dd($recentProductsIds);*/


        // Get recently viewed products
        $recentlyViewedProducts = Product::with('brand')->whereIn('id',$recentProductsIds)->get()->toArray();
        /*dd($recentlyViewedProducts);*/

        // Get group products (Product Colors)
        $groupProducts = array();
        if(!empty($productDetails['group_code'])){
            $groupProducts = Product::select('id','product_image')->where('id','!=',$id)->where(['group_code'=>$productDetails['group_code'],'status'=>1])->get()->toArray();
            /*dd($groupProducts);*/
        }

        // Get all ratings of product
        $ratings = Rating::with('user')->where(['product_id'=>$id,'status'=>1])->get()->toArray();

        // Get average rating of product
        $ratingSum = Rating::where(['product_id'=>$id,'status'=>1])->sum('rating');
        $ratingCount = Rating::where(['product_id'=>$id,'status'=>1])->count();

        // Get star rating
        $ratingOneStarCount = Rating::where(['product_id'=>$id,'status'=>1,'rating'=>1])->count();
        $ratingTwoStarCount = Rating::where(['product_id'=>$id,'status'=>1,'rating'=>2])->count();
        $ratingThreeStarCount = Rating::where(['product_id'=>$id,'status'=>1,'rating'=>3])->count();
        $ratingFourStarCount = Rating::where(['product_id'=>$id,'status'=>1,'rating'=>4])->count();
        $ratingFiveStarCount = Rating::where(['product_id'=>$id,'status'=>1,'rating'=>5])->count();

        // Compute average rating
        $avgRating = 0;
        $avgStarRating = 0;
        if($ratingCount>0){
            $avgRating = round($ratingSum/$ratingCount,2);
            $avgStarRating = round($ratingSum/$ratingCount);
        }

        // Calculate total stock
        $totalStock = ProductsAttribute::where('product_id',$id)->sum('stock');
        $meta_title = $productDetails['meta_title'];
        $meta_description = $productDetails['meta_description'];
        $meta_keywords = $productDetails['meta_keywords'];
        return view('front.products.detail')->with(compact('productDetails','categoryDetails','totalStock','similarProducts','recentlyViewedProducts','groupProducts','meta_title','meta_description','meta_keywords','ratings','avgRating','avgStarRating','ratingOneStarCount','ratingTwoStarCount','ratingThreeStarCount','ratingFourStarCount','ratingFiveStarCount'));
    }

    public function detail($id){
        $productDetails = Product::with(['section','category','brand','attributes'=>function($query){
            $query->where('stock','>',0)->where('status',1);
        },'images','vendor'])->find($id)->toArray();
        $categoryDetails = Category::categoryDetails($productDetails['category']['url']);

        // Get similar products
        $similarProducts = Product::with('brand')->where('category_id',$productDetails['category']['id'])->where('id','!=',$id)->limit(4)->inRandomOrder()->get()->toArray();
        /*dd($similarProducts);*/

        // Set session for recently viewed products
        if(empty(Session::get('session_id'))){
            $session_id = md5(uniqid(rand(), true));
        }else{
            $session_id = Session::get('session_id');
        }

        Session::put('session_id',$session_id);

        // Insert product in table if does not already exists
        $countRecentlyViewedProducts = DB::table('recently_viewed_products')->where(['product_id'=>$id,'session_id'=>$session_id])->count();
        if($countRecentlyViewedProducts==0){
            DB::table('recently_viewed_products')->insert(['product_id'=>$id,'session_id'=>$session_id]);
        }

        // Get recently viewed products IDs
        $recentProductsIds = DB::table('recently_viewed_products')->select('product_id')->where('product_id','!=',$id)->where('session_id',$session_id)->inRandomOrder()->get()->take(4)->pluck('product_id');
        /*dd($recentProductsIds);*/


        // Get recently viewed products
        $recentlyViewedProducts = Product::with('brand')->whereIn('id',$recentProductsIds)->get()->toArray();
        /*dd($recentlyViewedProducts);*/

        // Get group products (Product Colors)
        $groupProducts = array();
        if(!empty($productDetails['group_code'])){
            $groupProducts = Product::select('id','product_image')->where('id','!=',$id)->where(['group_code'=>$productDetails['group_code'],'status'=>1])->get()->toArray();
            /*dd($groupProducts);*/
        }

        // Get all ratings of product
        $ratings = Rating::with('user')->where(['product_id'=>$id,'status'=>1])->get()->toArray();

        // Get average rating of product
        $ratingSum = Rating::where(['product_id'=>$id,'status'=>1])->sum('rating');
        $ratingCount = Rating::where(['product_id'=>$id,'status'=>1])->count();

        // Get star rating
        $ratingOneStarCount = Rating::where(['product_id'=>$id,'status'=>1,'rating'=>1])->count();
        $ratingTwoStarCount = Rating::where(['product_id'=>$id,'status'=>1,'rating'=>2])->count();
        $ratingThreeStarCount = Rating::where(['product_id'=>$id,'status'=>1,'rating'=>3])->count();
        $ratingFourStarCount = Rating::where(['product_id'=>$id,'status'=>1,'rating'=>4])->count();
        $ratingFiveStarCount = Rating::where(['product_id'=>$id,'status'=>1,'rating'=>5])->count();

        // Get average rating
        $avgRating = 0;
        $avgStarRating = 0;
        if($ratingCount>0){
            $avgRating = round($ratingSum/$ratingCount,2);
            $avgStarRating = round($ratingSum/$ratingCount);
        }

        // Calculate total stock
        $totalStock = ProductsAttribute::where('product_id',$id)->sum('stock');
        $meta_title = $productDetails['meta_title'];
        $meta_description = $productDetails['meta_description'];
        $meta_keywords = $productDetails['meta_keywords'];
        return view('front.products.detail')->with(compact('productDetails','categoryDetails','totalStock','similarProducts','recentlyViewedProducts','groupProducts','meta_title','meta_description','meta_keywords','ratings','avgRating','avgStarRating','ratingOneStarCount','ratingTwoStarCount','ratingThreeStarCount','ratingFourStarCount','ratingFiveStarCount'));
    }

    // Get product prices
    public function getProductPrice(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $getDiscountAttributePrice = Product::getDiscountAttributePrice($data['product_id'],$data['size']);
            /*echo "<pre>"; print_r($getDiscountAttributePrice); die;*/
            // Calculate final price
            if($data['currency']!="PHP"){
                $getCurrency = Currency::where('currency_code',$data['currency'])->first()->toArray();
                $getDiscountAttributePrice['product_price'] =  round($getDiscountAttributePrice['product_price']/$getCurrency['exchange_rate'],2);
                $getDiscountAttributePrice['final_price'] =  round($getDiscountAttributePrice['final_price']/$getCurrency['exchange_rate'],2);
                $getDiscountAttributePrice['discount'] =  round($getDiscountAttributePrice['discount']/$getCurrency['exchange_rate'],2);
            }
            return $getDiscountAttributePrice;
        }
    }

    public function cartAdd(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/

            // Forget the coupon sessions
            Session::forget('couponAmount');
            Session::forget('couponCode');

            if($data['quantity']<=0){
                $data['quantity']=1;
            }

            // Check product stock is available or not
            $getProductStock = ProductsAttribute::getProductStock($data['product_id'],$data['size']);
            if($getProductStock<$data['quantity']){
                return redirect()->back()->with('error_message','Required Quantity is not available!');
            }

            // Generate session ID if not exists
            $session_id = Session::get('session_id');
            if(empty($session_id)){
                $session_id = Session::getId();
                Session::put('session_id',$session_id);
            }

            // Check product if already exists in the user cart
            if(Auth::check()){
                // User is logged in
                $user_id = Auth::user()->id;
                $countProducts = Cart::where(['product_id'=>$data['product_id'],'size'=>$data['size'],'user_id'=>$user_id])->count();
            }else{
                // User is not logged in
                $user_id = 0;
                $countProducts = Cart::where(['product_id'=>$data['product_id'],'size'=>$data['size'],'session_id'=>$session_id])->count();
            }

            if($countProducts>0){
                return redirect()->back()->with('error_message','Product already exists in Cart!');
            }

            // Save product in carts table
            $item = new Cart;
            $item->session_id = $session_id;
            $item->user_id = $user_id;
            $item->product_id = $data['product_id'];
            $item->size = $data['size'];
            $item->quantity = $data['quantity'];
            $item->save();
            return redirect()->back()->with('success_message','Product has been added in Cart! <a style="text-decoration:underline !important" href="/cart">View Cart</a>');
        }
    }

    // Display cart
    public function cart(){
        $getCartItems = Cart::getCartItems();
        /*dd($getCartItems);*/
        $meta_title = "Shopping Cart - WMSU TBI";
        $meta_keywords = "shopping cart, WMSU TBI";
        return view('front.products.cart')->with(compact('getCartItems','meta_title','meta_keywords'));
    }

    // Update cart
    public function cartUpdate(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/

            // Forget the coupon sessions
            Session::forget('couponAmount');
            Session::forget('couponCode');

            // Get cart details
            $cartDetails = Cart::find($data['cartid']);

            // Get available product stock
            $availableStock = ProductsAttribute::select('stock')->where(['product_id'=>$cartDetails['product_id'],'size'=>$cartDetails['size']])->first()->toArray();

            /*echo "<pre>"; print_r($availableStock); die;*/

            // Check if desired stock from user is available
            if($data['qty']>$availableStock['stock']){
                $getCartItems = Cart::getCartItems();
                return response()->json([
                    'status'=>false,
                    'message'=>'Product Stock is not available',
                    'view'=>(String)View::make('front.products.cart_items')->with(compact('getCartItems')),
                    'headerview'=>(String)View::make('front.layout.header_cart_items')->with(compact('getCartItems'))
                ]);
            }

            // Check if product size is available
            $availableSize = ProductsAttribute::where(['product_id'=>$cartDetails['product_id'],'size'=>$cartDetails['size'],'status'=>1])->count();
            if($availableSize==0){
                $getCartItems = Cart::getCartItems();
                return response()->json([
                    'status'=>false,
                    'message'=>'Product Size is not available. Please remove this Product and choose another one!',
                    'view'=>(String)View::make('front.products.cart_items')->with(compact('getCartItems')),
                    'headerview'=>(String)View::make('front.layout.header_cart_items')->with(compact('getCartItems'))
                ]);
            }

            // Update the quantity
            Cart::where('id',$data['cartid'])->update(['quantity'=>$data['qty']]);
            $getCartItems = Cart::getCartItems();
            /*dd($getCartItems);*/
            $currency = $data['currency'];
            $totalCartItems = totalCartItems();
            Session::forget('couponAmount');
            Session::forget('couponCode');
            return response()->json([
                'status'=>true,
                'totalCartItems'=>$totalCartItems,
                'currency'=>$currency,
                'view'=>(String)View::make('front.products.cart_items')->with(compact('getCartItems','currency')),
                'headerview'=>(String)View::make('front.layout.header_cart_items')->with(compact('getCartItems'))
            ]);
        }
    }

    // Delete cart item
    public function cartDelete(Request $request){
        if($request->ajax()){
            Session::forget('couponAmount');
            Session::forget('couponCode');
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            // Forget the coupon sessions
            Session::forget('couponAmount');
            Session::forget('couponCode');
            Cart::where('id',$data['cartid'])->delete();
            $getCartItems = Cart::getCartItems();
            $totalCartItems = totalCartItems();
            if(!empty($data['currency'])){
                $currency = $data['currency'];
            }else{
                $currency = "PHP";
            }
            return response()->json([
                'totalCartItems'=>$totalCartItems,
                'currency'=>$currency,
                'view'=>(String)View::make('front.products.cart_items')->with(compact('getCartItems','currency')),
                'headerview'=>(String)View::make('front.layout.header_cart_items')->with(compact('getCartItems'))
            ]);
        }
    }

    // Apply coupons
    public function applyCoupon(Request $request){
        if($request->ajax()){
            $data = $request->all();
            Session::forget('couponAmount');
            Session::forget('couponCode');
            /*echo "<pre>"; print_r($data); die;*/
            $getCartItems = Cart::getCartItems();
            $totalCartItems = totalCartItems();
            $couponCount = Coupon::where('coupon_code',$data['code'])->count();
            // Check if the coupon is valid
            if($couponCount==0){
                return response()->json([
                'status'=>false,
                'totalCartItems'=>$totalCartItems,
                'message'=>'The coupon is not valid!',
                'view'=>(String)View::make('front.products.cart_items')->with(compact('getCartItems')),
                'headerview'=>(String)View::make('front.layout.header_cart_items')->with(compact('getCartItems'))
                ]);
            }else{
                // Check for other coupon conditions

                // Get coupon details
                $couponDetails = Coupon::where('coupon_code',$data['code'])->first();

                // Check if coupon is active
                if($couponDetails->status==0){
                    $message = "The coupon is not active!";
                }

                // Check if coupon is expired
                $expiry_date = $couponDetails->expiry_date;
                $current_date = date('Y-m-d');
                if($expiry_date < $current_date){
                    $message = "The coupon is expired!";
                }

                // Check if coupon is for single time
                if($couponDetails->coupon_type=="Single Time"){
                    // Check in orders table if coupon already availed by the user
                    $couponCount = Order::where(['coupon_code'=>$data['code'],'user_id'=>Auth::user()->id])->count();
                    if($couponCount>=1){
                        $message = "This coupon code is already availed by you!";
                    }
                }

                // Check if coupon is from selected categories 
                // Get all selected categories from coupon and convert to array
                $catArr = explode(",",$couponDetails->categories);

                // Check if any cart item not belong to coupon category
                $total_amount = 0;
                foreach ($getCartItems as $key => $item) {
                    if(!in_array($item['product']['category_id'],$catArr)){
                        $message = "This coupon code is not for one of the selected products.";
                    }
                    $attrPrice = Product::getDiscountAttributePrice($item['product_id'],$item['size']);
                    /*echo "<pre>"; print_r($attrPrice); die;*/
                    $total_amount = $total_amount + ($attrPrice['final_price']*$item['quantity']);
                }

                // Check if coupon is from selected users
                // Get all selected users from coupon and convert to array
                if(isset($couponDetails->users)&&!empty($couponDetails->users)){
                    $usersArr = explode(",",$couponDetails->users);

                    if(count($usersArr)){
                        // Get User Id's of all selected users
                        foreach ($usersArr as $key => $user) {
                            $getUserId = User::select('id')->where('email',$user)->first()->toArray();
                            $usersId[] = $getUserId['id'];
                        }

                        // Check if any cart item not belong to coupon user 
                        foreach ($getCartItems as $item) {
                            if(!in_array($item['user_id'],$usersId)){
                                $message = "This coupon code is not for you. Try with valid coupon code!";
                            }
                        }
                    }
                }
                if($couponDetails->vendor_id>0){
                    $productIds = Product::select('id')->where('vendor_id',$couponDetails->vendor_id)->pluck('id')->toArray();
                    // Check if coupon belongs to vendor products
                    foreach ($getCartItems as $item) {
                        if(!in_array($item['product']['id'],$productIds)){
                                $message = "This coupon code is not for you. Try with valid coupon code!";
                        }
                    }
                }

                // If error message is there
                if(isset($message)){
                    return response()->json([
                    'status'=>false,
                    'totalCartItems'=>$totalCartItems,
                    'message'=>$message,
                    'view'=>(String)View::make('front.products.cart_items')->with(compact('getCartItems')),
                    'headerview'=>(String)View::make('front.layout.header_cart_items')->with(compact('getCartItems'))
                    ]);
                }else{
                    // Coupon code is correct

                    // Check if Coupon Amount type is Fixed or Percentage
                    if($couponDetails->amount_type=="Fixed"){
                        $couponAmount = $couponDetails->amount;
                    }else{
                        $couponAmount = $total_amount * ($couponDetails->amount/100);
                    }

                    $grand_total = $total_amount - $couponAmount;

                    // Add coupon code & amount in session variables
                    Session::put('couponAmount',$couponAmount);
                    Session::put('couponCode',$data['code']);

                    $message = "Coupon Code successfully applied. You are availing discount!";

                    return response()->json([
                    'status'=>true,
                    'totalCartItems'=>$totalCartItems,
                    'couponAmount'=>$couponAmount,
                    'grand_total'=>$grand_total,
                    'message'=>$message,
                    'view'=>(String)View::make('front.products.cart_items')->with(compact('getCartItems')),
                    'headerview'=>(String)View::make('front.layout.header_cart_items')->with(compact('getCartItems'))
                    ]);

                }
            }
        }
    }

    // Handle checkout process
    public function checkout(Request $request){
        $countries = Country::where('status',1)->get()->toArray();
        $barangay = Barangay::all()->toArray();
        $getCartItems = Cart::getCartItems();
        /*dd($getCartItems);*/
        // Check the cart is not empty
        if(count($getCartItems)==0){
            $message = "Shopping Cart is empty! Please add products to checkout";
            return redirect('cart')->with('error_message',$message);
        }
        // Calculate final price
        $total_price = 0;
        $total_weight = 0;
        foreach ($getCartItems as $item) {
            /*echo "<pre>"; print_r($item); die;*/
            $attrPrice = Product::getDiscountAttributePrice($item['product_id'],$item['size']);
            $total_price = $total_price + ($attrPrice['final_price']*$item['quantity']);
            $product_weight = $item['product']['product_weight'];
            $total_weight = $total_weight+$product_weight;
        }
        $deliveryAddresses = DeliveryAddress::deliveryAddresses();
        foreach ($deliveryAddresses as $key => $value) {
            $shippingCharges = ShippingCharge::getShippingCharges($total_weight,$value['barangay']);
            $deliveryAddresses[$key]['shipping_charges'] = $shippingCharges;
            // COD pincode is available or not
            $deliveryAddresses[$key]['codpincodeCount'] = DB::table('cod_pincodes')->where('pincode',$value['pincode'])->count();
            // Prepaid pincode is available or Not
            $deliveryAddresses[$key]['prepaidpincodeCount'] = DB::table('prepaid_pincodes')->where('pincode',$value['pincode'])->count();
        }
        /*dd($deliveryAddresses);*/
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            // Website security
            foreach ($getCartItems as $item) {
                // Prevent disabled products to order
                $product_status = Product::getProductStatus($item['product_id']);
                if($product_status==0){
                    /*Product::deleteCartProduct($item['product_id']);
                    $message = "One of the product is disabled! Please try again.";*/
                    $message = $item['product']['product_name']." with ".$item['size']." Size is not available. Please remove from cart and choose some other product.";
                    return redirect('/cart')->with('error_message',$message);
                }
                // Prevent sold out products to order
                $getProductStock = ProductsAttribute::getProductStock($item['product_id'],$item['size']);
                if($getProductStock==0){
                    /*Product::deleteCartProduct($item['product_id']);
                    $message = "One of the product is sold out! Please try again.";*/
                    $message = $item['product']['product_name']." with ".$item['size']." Size is not available. Please remove from cart and choose some other product.";
                    return redirect('/cart')->with('error_message',$message);
                }
                // Prevent disabled attributes to order
                $getAttributeStatus = ProductsAttribute::getAttributeStatus($item['product_id'],$item['size']);
                if($getAttributeStatus==0){
                    /*Product::deleteCartProduct($item['product_id']);
                    $message = "One of the product attribute is disabled! Please try again.";*/
                    $message = $item['product']['product_name']." with ".$item['size']." Size is not available. Please remove from cart and choose some other product.";
                    return redirect('/cart')->with('error_message',$message);
                }
                // Prevent disabled categories products to order
                $getCategoryStatus = Category::getCategoryStatus($item['product']['category_id']);
                if($getCategoryStatus==0){
                    //Product::deleteCartProduct($item['product_id']);
                    //$message = "One of the product is disabled! Please try again.";
                    $message = $item['product']['product_name']." with ".$item['size']." Size is not available. Please remove from cart and choose some other product.";
                    return redirect('/cart')->with('error_message',$message);
                }
            }

            // Delivery address validation
            if(empty($data['address_id'])){
                $message = "Please select Delivery Address!";
                return redirect()->back()->with('error_message',$message);
            }

            // Payment method validation
            if(empty($data['payment_gateway'])){
                $message = "Please select Payment Method!";
                return redirect()->back()->with('error_message',$message);
            }

            // Agree to T&C validation
            if(empty($data['accept'])){
                $message = "Please agree to T&C!";
                return redirect()->back()->with('error_message',$message);
            }

            /*echo "<pre>"; print_r($data); die;*/

            // Get delivery address from address_id
            $deliveryAddress = DeliveryAddress::where('id',$data['address_id'])->first()->toArray();
            /*dd($deliveryAddress);*/

            // Set payment M=method as COD if COD is selected from user otherwise set as Prepaid
            if($data['payment_gateway']=="COD"){
                $payment_method = "COD";
                $order_status = "New";
            }else{
                $payment_method = "Prepaid";
                $order_status = "Pending";   
            }

            DB::beginTransaction();

            // Fetch order total price
            $total_price = 0;
            foreach($getCartItems as $item){
                $getDiscountAttributePrice = Product::getDiscountAttributePrice($item['product_id'],$item['size']);
                $total_price = $total_price + ($getDiscountAttributePrice['final_price'] * $item['quantity']);
            }

            // Calculate shipping harges
            $shipping_charges = 0;

            // Get shipping charges
            $shipping_charges = ShippingCharge::getShippingCharges($total_weight,$deliveryAddress['barangay']);

            // Calculate grand total
            $grand_total = $total_price + $shipping_charges - Session::get('couponAmount');

            // Insert grand total in session variable
            Session::put('grand_total',$grand_total);

            // Insert order details
            $order = new Order;
            $order->user_id = Auth::user()->id;
            $order->name = $deliveryAddress['name'];
            $order->address = $deliveryAddress['address'];
            $order->city = $deliveryAddress['city'];
            $order->state = $deliveryAddress['barangay'];
            $order->country = $deliveryAddress['country'];
            $order->pincode = $deliveryAddress['pincode'];
            $order->mobile = $deliveryAddress['mobile'];
            $order->email = Auth::user()->email;
            $order->shipping_charges = $shipping_charges;
            $order->coupon_code = Session::get('couponCode');
            $order->coupon_amount = Session::get('couponAmount');
            $order->order_status = $order_status;
            $order->payment_method = $payment_method;
            $order->payment_gateway = $data['payment_gateway'];
            $order->grand_total = $grand_total;
            $order->save();
            $order_id = DB::getPdo()->lastInsertId();

            $vendorCommission = 0;

            // Set cart informations
            foreach($getCartItems as $item){
                $cartItem = new OrdersProduct;
                $cartItem->order_id = $order_id;
                $cartItem->user_id = Auth::user()->id;
                $getProductDetails = Product::select('product_code','product_name','product_color','admin_id','vendor_id')->where('id',$item['product_id'])->first()->toArray();
                /*dd($getProductDetails);*/
                $cartItem->admin_id = $getProductDetails['admin_id'];
                $cartItem->vendor_id = $getProductDetails['vendor_id'];
                $cartItem->item_status = "New";
                if($getProductDetails['vendor_id']>0){
                    $vendorCommission = Vendor::getVendorCommission($getProductDetails['vendor_id']);
                }
                $cartItem->commission = $vendorCommission;
                $cartItem->product_id = $item['product_id'];
                $cartItem->product_code = $getProductDetails['product_code'];
                $cartItem->product_name = $getProductDetails['product_name'];
                $cartItem->product_color = $getProductDetails['product_color'];
                $cartItem->product_size = $item['size'];
                $getDiscountAttributePrice = Product::getDiscountAttributePrice($item['product_id'],$item['size']);
                $cartItem->product_price = $getDiscountAttributePrice['final_price'];

                $getProductStock = ProductsAttribute::getProductStock($item['product_id'],$item['size']);

                if($item['quantity']>$getProductStock){
                    $message = $getProductDetails['product_name']." with ".$item['size']." Quantity is not available. Please reduce its quantity and try again.";
                    return redirect('/cart')->with('error_message',$message);
                }

                $cartItem->product_qty = $item['quantity'];
                $cartItem->save();

                // Reduce stock script starts
                $getProductStock = ProductsAttribute::getProductStock($item['product_id'],$item['size']);
                $newStock = $getProductStock - $item['quantity'];
                ProductsAttribute::where(['product_id'=>$item['product_id'],'size'=>$item['size']])->update(['stock'=>$newStock]);
                // Reduce Stock Script Ends
            }

            // Insert order ID in session variable
            Session::put('order_id',$order_id);
            DB::commit();
            $orderDetails = Order::with('orders_products')->where('id',$order_id)->first()->toArray();
            if($data['payment_gateway']=="COD"){
                // Send order email
                $email = Auth::user()->email;
                $messageData = [
                    'email' => $email,
                    'name' => Auth::user()->name,
                    'order_id' => $order_id,
                    'orderDetails' => $orderDetails
                ];
                Mail::send('emails.order',$messageData,function($message)use($email){
                    $message->to($email)->subject('Order Placed - WMSU TBI');
                });
            }else if($data['payment_gateway']=="ONLINE"){

                return redirect()->route('gcash.pay');
            } else{
                echo "Other Prepaid payment methods coming soon";
            }
            return redirect('thanks');
        }
        return view('front.products.checkout')->with(compact('deliveryAddresses','countries','getCartItems','total_price', 'barangay'));
    }

    public function thanks(){
        if(Session::has('order_id')){
            // Empty the cart
            Cart::where('user_id',Auth::user()->id)->delete();
            return view('front.products.thanks');
        }else{
            return redirect('cart');
        }
    }

    public function checkPincode(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            // COD pincode is available or not
            $codPincodeCount = DB::table('cod_pincodes')->where('pincode',$data['pincode'])->count();
            // Prepaid pincode is available or not
            $prepaidPincodeCount = DB::table('prepaid_pincodes')->where('pincode',$data['pincode'])->count();
            if($codPincodeCount==0 && $prepaidPincodeCount==0){
                echo "This pincode is not available for Delivery";
            }else{
                echo "This pincode is available for Delivery";
            }
        }
    }
}
