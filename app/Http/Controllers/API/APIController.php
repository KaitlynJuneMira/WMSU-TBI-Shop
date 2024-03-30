<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CmsPage;
use App\Models\Section;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductsFilter;
use App\Models\Cart;
use App\Models\ProductsAttribute;
use App\Models\ShippingCharge;
use App\Models\Order;
use App\Models\OrdersProduct;
use Validator;
use DB;


class APIController extends Controller
{
    // User registration
    public function registerUser(Request $request){
        if($request->isMethod('post')){
            $data = $request->input();
            /*echo "<pre>"; print_r($data); die;*/
            // Enter validation
            $rules = [
                "name" => "required",
                "email" => "required|email|unique:users",
                "password" => "required"
            ];
            // Custom error messages
            $customMessages = [
                "name.required" => "Name is required",
                "email.required" => "Email is required",
                "email.unique" => "Email already exists",
                "password.required" => "Password is required"
            ];

            $validator = Validator::make($data,$rules,$customMessages);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            // Get user informations
            $user = new User;
            $user->name = $data['name'];
            $user->mobile = $data['mobile'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->status = 1;
            $user->save();
            return response()->json(['status'=>true,'message'=>'User registered successfully!'],201);
        }
    }

    // User login
    public function loginUser(Request $request){
        if($request->isMethod('post')){
            $data = $request->input();
            /*echo "<pre>"; print_r($data); die;*/
            // Enter validation
            $rules = [
                "email" => "required|email|exists:users",
                "password" => "required"
            ];
            // Custom error messages
            $customMessages = [
                "email.required" => "Email is required",
                "email.exists" => "Email does not exists",
                "password.required" => "Password is required"
            ];

            $validator = Validator::make($data,$rules,$customMessages);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }

            // Verify User Email
            $userCount = User::where('email',$data['email'])->count();
            if($userCount>0){
                // Fetch User Details
                $userDetails = User::where('email',$data['email'])->first();

                // Verify the password
                if(password_verify($data['password'],$userDetails->password)){
                    return response()->json([
                        "userDetails"=>$userDetails,
                        "status"=>true,
                        "message"=>"User Login Successfully!"
                    ],201);
                }else{
                    $message = "Password is Incorrect!";
                    return response()->json(['status'=>false,"message"=>$message],422);
                }
            }else{
                $message = "Email is Incorrect!";
                return response()->json(['status'=>false,"message"=>$message],422);
            }
        }
    }

    // Update user
    public function updateUser(Request $request){
        if($request->isMethod('post')){
            $data = $request->input();
            /*echo "<pre>"; print_r($data); die;*/
            // Enter validation
            $rules = [
                "name" => "required"
            ];
            // Custom error messages
            $customMessages = [
                "name.required" => "Name is required"
            ];

            $validator = Validator::make($data,$rules,$customMessages);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }

            // Verify User ID
            $userCount = User::where('id',$data['id'])->count();
            if($userCount>0){

                if(empty($data['address'])){
                    $data['address']="";
                }
                if(empty($data['city'])){
                    $data['city']="";
                }
                if(empty($data['state'])){
                    $data['state']="";
                }
                if(empty($data['country'])){
                    $data['country']="";
                }
                if(empty($data['pincode'])){
                    $data['pincode']="";
                }

                // Update User Details
                User::where('id',$data['id'])->update(['name'=>$data['name'],'address'=>$data['address'],'city'=>$data['city'],'state'=>$data['state'],'country'=>$data['country'],'pincode'=>$data['pincode']]);

                // Fetch User Details
                $userDetails = User::where('id',$data['id'])->first();

                return response()->json([
                    "userDetails"=>$userDetails,
                    "status"=>true,
                    "message"=>"User Updated Successfully!"
                ],201);
            }else{
                $message = "User does not exists!";
                return response()->json(['status'=>false,"message"=>$message],422);
            }


        }
    }

    // Fetch CMS page
    public function cmsPage(){
        $currentRoute = url()->current();
        $currentRoute = str_replace("http://127.0.0.1:8000/api/","",$currentRoute);
        $cmsRoutes = CmsPage::select('url')->where('status',1)->get()->pluck('url')->toArray();
        if(in_array($currentRoute,$cmsRoutes)){
            $cmsPageDetails = CmsPage::where('url',$currentRoute)->get();
            return response()->json([
                'cmsPageDetails'=>$cmsPageDetails,
                'status'=>true,
                "message"=>"Page details fetched successfully!"
            ],200);
        }else{
            $message = "Page does not exists!";
            return response()->json(['status'=>false,"message"=>$message],422);
        }
    }

    // Fetch categories
    public function menu(){
        $categories = Section::with('categories')->get();
        return response()->json(["categories"=>$categories],200);
    }

    public function listing($url){
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

            $categoryProducts = $categoryProducts->get();

            // Compute the final price
            foreach ($categoryProducts as $key => $value) {
                $getDiscountPrice = Product::getDiscountPrice($categoryProducts[$key]['id']);
                if($getDiscountPrice>0){
                    $categoryProducts[$key]['final_price'] = "PHP ".$getDiscountPrice;  
                }else{
                    $categoryProducts[$key]['final_price'] = "PHP ".$categoryProducts[$key]['product_price'];
                }
                $categoryProducts[$key]['product_image'] = url("/front/images/product_images/small/".$categoryProducts[$key]['product_image']);
            }

            return response()->json(["products" => $categoryProducts],200);
        }else{
            $message = "Category URL is Incorrect!";
            return response()->json(['status'=>false,"message"=>$message],422);
        }
    }

    public function detail($id){
        $productCount = Product::where(['id'=>$id,'status'=>1])->count();
        // Fetch product details
        if($productCount>0){
            $productDetails = Product::with(['section','category','brand','attributes'=>function($query){
            $query->where('stock','>',0)->where('status',1);
            },'images','vendor'])->where('id',$id)->get();

            // Compute final price
            foreach ($productDetails as $key => $value) {
                $getDiscountPrice = Product::getDiscountPrice($productDetails[$key]['id']);
                if($getDiscountPrice>0){
                    $productDetails[$key]['final_price'] = "PHP ".$getDiscountPrice;  
                }else{
                    $productDetails[$key]['final_price'] = "PHP ".$productDetails[$key]['product_price'];
                }
                $productDetails[$key]['product_image'] = url("/front/images/product_images/small/".$productDetails[$key]['product_image']);
            }

            return response()->json(['product'=>$productDetails],200);

        }else{
            $message = "Product is not available";
            return response()->json(['status'=>false,"message"=>$message],422);
        }
    }

    public function addtoCart(Request $request){
        if($request->isMethod('post')){
            $data = $request->input();

            // Check Product if already exists in the User Cart
            $countProducts = Cart::where(['product_id'=>$data['productid'],'size'=>$data['size'],'user_id'=>$data['userid']])->count();
            if($countProducts>0){
                $message = "Product already exists in Cart!";
                return response()->json(['status'=>false,"message"=>$message],422);
            }

            // Save Product in carts table
            $item = new Cart;
            $item->session_id = 0;
            $item->user_id = $data['userid'];
            $item->product_id = $data['productid'];
            $item->size = $data['size'];
            $item->quantity = 1;
            $item->source = "App";
            $item->save();

            return response()->json([
                'status'=>true,
                "message"=>"Product successfully added in Cart!"
            ],200);

        }
        
    }

    public function cart($userid){
        // Fetch cart items
        $getCartItems = Cart::with(['product'=>function($query){
                $query->select('id','category_id','product_name','product_code','product_color','product_image','product_weight','product_price');
            }])->orderby('id','Desc')->where('user_id',$userid)->get();
        // Compute final price
        foreach ($getCartItems as $key => $item) {
            $getDiscountPrice = Product::getDiscountAttributePrice($item['product_id'],$item['size']);
            if($getDiscountPrice>0){
                $getCartItems[$key]['product']['final_price'] = "PHP ".$getDiscountPrice['final_price'];  
            }else{
                $getCartItems[$key]['product']['final_price'] = "PHP ".$item['product']['product_price'];
            }
            $getCartItems[$key]['product']['product_image'] = url("/front/images/product_images/small/".$item['product']['product_image']);
        }

        /*dd($getCartItems);*/

        return response()->json(["products"=>$getCartItems],200);
    }

    public function checkout($userid){
        // Fetch cart items in checkout
        $getCartItems = Cart::with(['product'=>function($query){
                $query->select('id','category_id','product_name','product_code','product_color','product_image','product_weight','product_price');
            }])->orderby('id','Desc')->where('user_id',$userid)->get();
        // Compute final price
        $total_price = 0;
        foreach ($getCartItems as $key => $item) {
            $getDiscountPrice = Product::getDiscountAttributePrice($item['product_id'],$item['size']);
            if($getDiscountPrice>0){
                $getCartItems[$key]['product']['final_price'] = "PHP ".$getDiscountPrice['final_price'];
                $total_price = $total_price + $getDiscountPrice['final_price'];  
            }else{
                $getCartItems[$key]['product']['final_price'] = "PHP ".$item['product']['product_price'];
                $total_price = $total_price + $item['product']['product_price'];
            }
            $getCartItems[$key]['product']['product_image'] = url("/front/images/product_images/small/".$item['product']['product_image']);
        }
        // Compute total price
        foreach ($getCartItems as $key => $item) {
            $getCartItems[$key]['product']['total_price'] = $total_price;    
            $getCartItems[$key]['product']['key'] = $key;    
        }

        /*dd($getCartItems);*/

        return response()->json(["products"=>$getCartItems],200);
    }

    // Deleting cart item
    public function deleteCartItem($cartid){
        Cart::where('id',$cartid)->delete();
        return response()->json([
            'status'=>true,
            'message'=>'Product successfully deleted from Cart!'
        ],200);
    }

    public function placeOrder($userid,Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            // Enter validation
            $rules = [
                "name" => "required",
                "address" => "required",
                "city" => "required"
            ];
            // Set custom error messages
            $customMessages = [
                "name.required" => "Name is required",
                "address.required" => "Address is required",
                "city.required" => "City is required"
            ];

            $validator = Validator::make($data,$rules,$customMessages);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            // Get cart items
            $getCartItems = Cart::with(['product'=>function($query){
                $query->select('id','category_id','product_name','product_code','product_color','product_image','product_weight','product_price');
            }])->orderby('id','Desc')->where('user_id',$userid)->get();

            // Calculate total price and weight
            $total_price = 0;
            $total_weight = 0;
            // Get final price
            foreach ($getCartItems as $key => $item) {
                $getDiscountPrice = Product::getDiscountAttributePrice($item['product_id'],$item['size']);
                if($getDiscountPrice>0){
                    $getCartItems[$key]['product']['final_price'] = "PHP ".$getDiscountPrice['final_price'];
                    $total_price = $total_price + $getDiscountPrice['final_price'];  
                }else{
                    $getCartItems[$key]['product']['final_price'] = "PHP ".$item['product']['product_price'];
                    $total_price = $total_price + $item['product']['product_price'];
                }
                $getCartItems[$key]['product']['product_image'] = url("/front/images/product_images/small/".$item['product']['product_image']);

                $getProductWeight = Product::select('product_weight')->where('id',$item['product_id'])->first();
                $total_weight = $total_weight+$getProductWeight->product_weight;
            }

            // For COD order placement
            $payment_method = "COD";
            $order_status = "New";


            DB::beginTransaction();

            // Fetch order total price
            $total_price = 0;
            foreach($getCartItems as $item){
                $getDiscountAttributePrice = Product::getDiscountAttributePrice($item['product_id'],$item['size']);
                $total_price = $total_price + ($getDiscountAttributePrice['final_price'] * $item['quantity']);
            }

            // Calculate shipping charges
            $shipping_charges = 0;

            // Get shipping charges
            $shipping_charges = ShippingCharge::getShippingCharges($total_weight,$data['country']);

            // Calculate grand total
            $grand_total = $total_price + $shipping_charges;

            // Insert order details
            $order = new Order;
            $order->user_id = $userid;
            $order->name = $data['name'];
            $order->address = $data['address'];
            $order->city = $data['city'];
            $order->state = $data['state'];
            $order->country = $data['country'];
            $order->pincode = $data['pincode'];
            $order->mobile = $data['mobile'];

            // Get user email
            $getUserEmail = User::select('email')->where('id',$userid)->first();

            // Get order informations
            $order->email = $getUserEmail->email;
            $order->shipping_charges = $shipping_charges;
            $order->order_status = $order_status;
            $order->payment_method = $payment_method;
            $order->payment_gateway = "COD";
            $order->grand_total = $grand_total;
            $order->save();
            $order_id = DB::getPdo()->lastInsertId();

            // Get cart infomations
            foreach($getCartItems as $item){
                $cartItem = new OrdersProduct;
                $cartItem->order_id = $order_id;
                $cartItem->user_id = $userid;
                $getProductDetails = Product::select('product_code','product_name','product_color','admin_id','vendor_id')->where('id',$item['product_id'])->first()->toArray();
                /*dd($getProductDetails);*/
                $cartItem->admin_id = $getProductDetails['admin_id'];
                $cartItem->vendor_id = $getProductDetails['vendor_id'];
                $cartItem->product_id = $item['product_id'];
                $cartItem->product_code = $getProductDetails['product_code'];
                $cartItem->product_name = $getProductDetails['product_name'];
                $cartItem->product_color = $getProductDetails['product_color'];
                $cartItem->product_size = $item['size'];
                $getDiscountAttributePrice = Product::getDiscountAttributePrice($item['product_id'],$item['size']);
                $cartItem->product_price = $getDiscountAttributePrice['final_price'];
                $cartItem->product_qty = $item['quantity'];
                $cartItem->save();

                // Reduce Stock Script Starts
                $getProductStock = ProductsAttribute::getProductStock($item['product_id'],$item['size']);
                $newStock = $getProductStock - $item['quantity'];
                ProductsAttribute::where(['product_id'=>$item['product_id'],'size'=>$item['size']])->update(['stock'=>$newStock]);
                // Reduce Stock Script Ends
            }

            DB::commit();

            return response()->json([
            'status'=>true,
            'message'=>'Order successfully placed!'
        ],200);


        }
    }

    // Fetch orders and its date ordered
    public function userOrders($userid){
        $orders = Order::where('user_id',$userid)->orderBy('id','Desc')->get();
        foreach ($orders as $key => $order) {
            $orders[$key]['created_on'] = date("d-m-Y H:i:s", strtotime($order->created_at));
        }
        return response()->json(['orders'=>$orders],200);
    }

}




