<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\OrderStatus;
use App\Models\OrderItemStatus;
use App\Models\OrdersLog;
use App\Models\User;
use App\Models\Message;
use App\Models\Reply;

use Illuminate\Support\Facades\Storage;
use Session;
use Auth;
use Dompdf\Dompdf;

class OrderController extends Controller
{
    // Show return refund query
   public function inbox(Request $request) {
        Session::put('page', 'inbox'); // Set the page session variable to 'inbox'
        try {
            $vendor_id = Auth::guard('admin')->user()->vendor_id;
            $tickets = Message::where('vendor_id', $vendor_id)->get();
            // Iterate through each ticket
            foreach ($tickets as $ticket) {
                // Get the video proof file path
                $videoPath = storage_path('app/' . $ticket->video_proof);
                // Check if the file exists
                if (file_exists($videoPath)) {
                    // Read the video file content
                    $videoContent = file_get_contents($videoPath);
                    // Add the video content to the ticket object
                    $ticket->videoContent = $videoContent;
                } else {
                    // Set video content to null if file doesn't exist
                    $ticket->videoContent = null;
                }
            }
            return view('admin.inbox.inbox', compact('tickets')); // Return the inbox view with tickets
        } catch (\Exception $e) {
            // Log the error or handle it accordingly
            return $e->getMessage();
        }
    }

    // Retrieve replied messages
    public function reply($messageid){
            $message = Message::where('id', $messageid)->first();
            $replies = Reply::where('message_id', $messageid)->get();
            return view('admin.inbox.reply', compact('message', 'replies'));
        }

    // Reply to messages
    public function Replied(Request $request){
            /* dd($request->all()); */
            Reply::create([
                'sender_id' => Auth::guard('admin')->user()->vendor_id,
                'receiver_id' => $request->receiver_id,
                'message_id' => $request->message_id,
                'message' => $request->message,
            ]);

            return redirect()->back();
        }

    // Display orders / check if the vendor account is approved or not
    public function orders(){
        Session::put('page','orders');
        $adminType = Auth::guard('admin')->user()->type;
        $vendor_id = Auth::guard('admin')->user()->vendor_id;
        if($adminType=="vendor"){
            $vendorStatus = Auth::guard('admin')->user()->status;
            // Not approved vendor account
            if($vendorStatus==0){
                return redirect("admin/update-vendor-details/personal")->with('error_message','Your Seller Account is not approved yet. Please make sure to fill your valid personal and business details');
            }
        }
        // Approved vendor account
        if($adminType=="vendor"){
            $orders = Order::with(['orders_products'=>function($query)use($vendor_id){
                $query->where('vendor_id',$vendor_id);
            }])->orderBy('id','Desc')->get()->toArray();
        }else{
            $orders = Order::with('orders_products')->orderBy('id','Desc')->get()->toArray();
        }
        // dd($orders);
        return view('admin.orders.orders')->with(compact('orders'));
    }

    // Display order details / check if the vendor account is approved or not
    public function orderDetails($id){
        Session::put('page','orders');
        $adminType = Auth::guard('admin')->user()->type;
        $vendor_id = Auth::guard('admin')->user()->vendor_id;
        if($adminType=="vendor"){
            $vendorStatus = Auth::guard('admin')->user()->status;
            // Not approved vendor account
            if($vendorStatus==0){
                return redirect("admin/update-vendor-details/personal")->with('error_message','Your Seller Account is not approved yet. Please make sure to fill your valid personal and business details');
            }
        }
        // Approved vendor account
        if($adminType=="vendor"){
            $orderDetails = Order::with(['orders_products'=>function($query)use($vendor_id){
                $query->where('vendor_id',$vendor_id);
            }])->where('id',$id)->first()->toArray();
        }else{
            $orderDetails = Order::with('orders_products')->where('id',$id)->first()->toArray();
        }
        $userDetails = User::where('id',$orderDetails['user_id'])->first()->toArray();
        $orderStatuses = OrderStatus::where('status',1)->get()->toArray();
        $orderItemStatuses = OrderItemStatus::where('status',1)->get()->toArray();
        $orderLog = OrdersLog::with('orders_products')->where('order_id',$id)->orderBy('id','Desc')->get()->toArray();
        /*dd($orderLog);*/

        // Calculate Total Items in Cart
        $total_items = 0;
        foreach ($orderDetails['orders_products'] as $product) {
            $total_items = $total_items + $product['product_qty'];
        }

        // Calculate Item Discount
        if($orderDetails['coupon_amount']>0){
            $item_discount = round($orderDetails['coupon_amount']/$total_items,2);
        }else{
            $item_discount = 0;
        }

        return view('admin.orders.order_details')->with(compact('orderDetails','userDetails','orderStatuses','orderItemStatuses','orderLog','item_discount'));
    }

    public function deleteOrder($id){
        // Delete Order
        Order::where('id',$id)->delete();
        $message = "Order has been deleted successfully!";
        return redirect()->back()->with('success_message',$message);
    }

    public function updateOrderStatus(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/

            // Update Order Status
            Order::where('id',$data['order_id'])->update(['order_status'=>$data['order_status']]);

            // Update Courier Name & Tracking Number
            if(!empty($data['courier_name'])&&!empty($data['tracking_number'])){
                Order::where('id',$data['order_id'])->update(['courier_name'=>$data['courier_name'],'tracking_number'=>$data['tracking_number']]);
            }

            // Update Order Log
            $log = new OrdersLog;
            $log->order_id = $data['order_id'];
            $log->order_status = $data['order_status'];
            $log->save();

            // Get Delivery Details
            $deliveryDetails = Order::select('mobile','email','name')->where('id',$data['order_id'])->first()->toArray();

            $orderDetails = Order::with('orders_products')->where('id',$data['order_id'])->first()->toArray();

            if(!empty($data['courier_name'])&&!empty($data['tracking_number'])){
                // Send Order Status Update Email
                $email = $deliveryDetails['email'];
                $messageData = [
                    'email' => $email,
                    'name' => $deliveryDetails['name'],
                    'order_id' => $data['order_id'],
                    'orderDetails' => $orderDetails,
                    'order_status' => $data['order_status'],
                    'courier_name' => $data['courier_name'],
                    'tracking_number' => $data['tracking_number'],
                ];
                Mail::send('emails.order_status',$messageData,function($message)use($email){
                    $message->to($email)->subject('Order Status Updated - WMSU TBI');
                });
            }else{
                // Send Order Status Update Email
                $email = $deliveryDetails['email'];
                $messageData = [
                    'email' => $email,
                    'name' => $deliveryDetails['name'],
                    'order_id' => $data['order_id'],
                    'orderDetails' => $orderDetails,
                    'order_status' => $data['order_status'],
                ];
                Mail::send('emails.order_status',$messageData,function($message)use($email){
                    $message->to($email)->subject('Order Status Updated - WMSU TBI');
                });   
            }
            $message = "Order Status has been updated successfully!";
            return redirect()->back()->with('success_message',$message);
        }
    }

    public function updateOrderItemStatus(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/

            // Update Order Item Status
            OrdersProduct::where('id',$data['order_item_id'])->update(['item_status'=>$data['order_item_status']]);

            // Update Courier Name & Tracking Number
            if(!empty($data['item_courier_name'])&&!empty($data['item_tracking_number'])){
                OrdersProduct::where('id',$data['order_item_id'])->update(['courier_name'=>$data['item_courier_name'],'tracking_number'=>$data['item_tracking_number']]);
            }

            $getOrderId = OrdersProduct::select('order_id')->where('id',$data['order_item_id'])->first()->toArray();

            // Update Order Log
            $log = new OrdersLog;
            $log->order_id = $getOrderId['order_id'];
            $log->order_item_id = $data['order_item_id'];
            $log->order_status = $data['order_item_status'];
            $log->save();

            // Get Delivery Details
            $deliveryDetails = Order::select('mobile','email','name')->where('id',$getOrderId['order_id'])->first()->toArray();

            $order_item_id = $data['order_item_id'];
            $orderDetails = Order::with(['orders_products'=>function($query)use($order_item_id){
                $query->where('id',$order_item_id);
            }])->where('id',$getOrderId['order_id'])->first()->toArray();

            if(!empty($data['item_courier_name'])&&!empty($data['item_tracking_number'])){
                // Send Order Status Update Email
                $email = $deliveryDetails['email'];
                $messageData = [
                    'email' => $email,
                    'name' => $deliveryDetails['name'],
                    'order_id' => $getOrderId['order_id'],
                    'orderDetails' => $orderDetails,
                    'order_status' => $data['order_item_status'],
                    'courier_name' => $data['item_courier_name'],
                    'tracking_number' => $data['item_tracking_number'],
                ];
                Mail::send('emails.order_item_status',$messageData,function($message)use($email){
                    $message->to($email)->subject('Order Status Updated - WMSU TBI');
                });
            }else{
                // Send Order Status Update Email
                $email = $deliveryDetails['email'];
                $messageData = [
                    'email' => $email,
                    'name' => $deliveryDetails['name'],
                    'order_id' => $getOrderId['order_id'],
                    'orderDetails' => $orderDetails,
                    'order_status' => $data['order_item_status'],
                ];
                Mail::send('emails.order_item_status',$messageData,function($message)use($email){
                    $message->to($email)->subject('Order Status Updated - WMSU TBI');
                });    
            }
            $message = "Order Item Status has been updated successfully!";
            return redirect()->back()->with('success_message',$message);
        }
    }

    // Generate order invoice
    public function viewOrderInvoice($order_id){
        $orderDetails = Order::with('orders_products')->where('id',$order_id)->first()->toArray();
        $userDetails = User::where('id',$orderDetails['user_id'])->first()->toArray();
        return view('admin.orders.order_invoice')->with(compact('orderDetails','userDetails'));
    }

    // Generate HTML template pdf invoice
    public function viewPDFInvoice($order_id){
        $orderDetails = Order::with('orders_products')->where('id',$order_id)->first()->toArray();
        $userDetails = User::where('id',$orderDetails['user_id'])->first()->toArray();
        $invoiceHTML = '<!DOCTYPE html>
        <html>
        <head>
            <title>HTML to API - Invoice</title>
            <meta content="width=device-width, initial-scale=1.0" name="viewport">
            <meta http-equiv="content-type" content="text-html; charset=utf-8">
            <style type="text/css">
                html, body, div, span, applet, object, iframe,
                h1, h2, h3, h4, h5, h6, p, blockquote, pre,
                a, abbr, acronym, address, big, cite, code,
                del, dfn, em, img, ins, kbd, q, s, samp,
                small, strike, strong, sub, sup, tt, var,
                b, u, i, center,
                dl, dt, dd, ol, ul, li,
                fieldset, form, label, legend,
                table, caption, tbody, tfoot, thead, tr, th, td,
                article, aside, canvas, details, embed,
                figure, figcaption, footer, header, hgroup,
                menu, nav, output, ruby, section, summary,
                time, mark, audio, video {
                    margin: 0;
                    padding: 0;
                    border: 0;
                    font: inherit;
                    font-size: 100%;
                    vertical-align: baseline;
                }
        
                html {
                    line-height: 1;
                }
        
                ol, ul {
                    list-style: none;
                }
        
                table {
                    border-collapse: collapse;
                    border-spacing: 0;
                }
        
                caption, th, td {
                    text-align: left;
                    font-weight: normal;
                    vertical-align: middle;
                }
        
                q, blockquote {
                    quotes: none;
                }
                q:before, q:after, blockquote:before, blockquote:after {
                    content: "";
                    content: none;
                }
        
                a img {
                    border: none;
                }
        
                article, aside, details, figcaption, figure, footer, header, hgroup, main, menu, nav, section, summary {
                    display: block;
                }
        
                body {
                    font-family: "Source Sans Pro", sans-serif;
                    font-weight: 300;
                    font-size: 12px;
                    margin: 0;
                    padding: 0;
                }
                body a {
                    text-decoration: none;
                    color: inherit;
                }
                body a:hover {
                    color: inherit;
                    opacity: 0.7;
                }
                body .container {
                    min-width: 500px;
                    margin: 0 auto;
                    padding: 0 20px;
                }
                body .clearfix:after {
                    content: "";
                    display: table;
                    clear: both;
                }
                body .left {
                    float: left;
                }
                body .right {
                    float: right;
                }
                body .helper {
                    display: inline-block;
                    height: 100%;
                    vertical-align: middle;
                }
                body .no-break {
                    page-break-inside: avoid;
                }
        
                header {
                    margin-top: 20px;
                    margin-bottom: 50px;
                }
                header figure {
                    float: left;
                    width: 60px;
                    height: 60px;
                    margin-right: 10px;
                    background-color: #d90429; /* Changed theme color */
                    border-radius: 50%;
                    text-align: center;
                }
                header figure img {
                    margin-top: 13px;
                }
                header .company-address {
                    float: left;
                    max-width: 150px;
                    line-height: 1.7em;
                }
                header .company-address .title {
                    color: #d90429; /* Changed theme color */
                    font-weight: 400;
                    font-size: 1.5em;
                    text-transform: uppercase;
                }
                header .company-contact {
                    float: right;
                    height: 60px;
                    padding: 0 10px;
                    background-color: #d90429; /* Changed theme color */
                    color: white;
                }
                header .company-contact span {
                    display: inline-block;
                    vertical-align: middle;
                }
                header .company-contact .circle {
                    width: 20px;
                    height: 20px;
                    background-color: white;
                    border-radius: 50%;
                    text-align: center;
                }
                header .company-contact .circle img {
                    vertical-align: middle;
                }
                header .company-contact .phone {
                    height: 100%;
                    margin-right: 20px;
                }
                header .company-contact .email {
                    height: 100%;
                    min-width: 100px;
                    text-align: right;
                }
        
                section .details {
                    margin-bottom: 55px;
                }
                section .details .client {
                    width: 50%;
                    line-height: 20px;
                }
                section .details .client .name {
                    color: #d90429; /* Changed theme color */
                }
                section .details .data {
                    width: 50%;
                    text-align: right;
                }
                section .details .title {
                    margin-bottom: 15px;
                    color: #d90429; /* Changed theme color */
                    font-size: 3em;
                    font-weight: 400;
                    text-transform: uppercase;
                }
                section table {
                    width: 100%;
                    border-collapse: collapse;
                    border-spacing: 0;
                    font-size: 0.9166em;
                }
                section table .qty, section table .unit, section table .total {
                    width: 15%;
                }
                section table .desc {
                    width: 55%;
                }
                section table thead {
                    display: table-header-group;
                    vertical-align: middle;
                    border-color: inherit;
                }
                section table thead th {
                    padding: 5px 10px;
                    background: #d90429; /* Changed theme color */
                    border-bottom: 5px solid #FFFFFF;
                    border-right: 4px solid #FFFFFF;
                    text-align: right;
                    color: white;
                    font-weight: 400;
                    text-transform: uppercase;
                }
                section table thead th:last-child {
                    border-right: none;
                }
                section table thead .desc {
                    text-align: left;
                }
                section table thead .qty {
                    text-align: center;
                }
                section table tbody td {
                    padding: 10px;
                    background: #E8F3DB;
                    color: #777777;
                    text-align: right;
                    border-bottom: 5px solid #FFFFFF;
                    border-right: 4px solid #E8F3DB;
                }
                section table tbody td:last-child {
                    border-right: none;
                }
                section table tbody h3 {
                    margin-bottom: 5px;
                    color: #d90429; /* Changed theme color */
                    font-weight: 600;
                }
                section table tbody .desc {
                    text-align: left;
                }
                section table tbody .qty {
                    text-align: center;
                }
                section table.grand-total {
                    margin-bottom: 45px;
                }
                section table.grand-total td {
                    padding: 5px 10px;
                    border: none;
                    color: #777777;
                    text-align: right;
                }
                section table.grand-total .desc {
                    background-color: transparent;
                }
                section table.grand-total tr:last-child td {
                    font-weight: 600;
                    color: #d90429; /* Changed theme color */
                    font-size: 1.18181818181818em;
                }
        
                footer {
                    margin-bottom: 20px;
                }
                footer .thanks {
                    margin-bottom: 40px;
                    color: #d90429; /* Changed theme color */
                    font-size: 1.16666666666667em;
                    font-weight: 600;
                }
                footer .notice {
                    margin-bottom: 25px;
                }
                footer .end {
                    padding-top: 5px;
                    border-top: 2px solid #d90429; /* Changed theme color */
                    text-align: center;
                }
            </style>
        </head>
        
        <body>
            <header class="clearfix">
                <div class="container">
                    <div class="company-address">
                        <h2 class="title">WMSU TBI SHOP</h2>
                        <p>
                            Zamboanga City
                        </p>
                    </div>
                </div>
            </header>
        
            <section>
                <div class="container">
                    <div class="details clearfix">
                        <div class="client left">
                            <p>INVOICE TO:</p>
                            <p class="name">'.$orderDetails['name'].'</p>
                            <p>'.$orderDetails['address'].', '.$orderDetails['city'].', '.$orderDetails['state'].', '.$orderDetails['country'].'-'.$orderDetails['pincode'].'</p>
                            <a href="mailto:'.$orderDetails['email'].'">'.$orderDetails['email'].'</a>
                        </div>
                        <div class="data right">
                            <div class="title">Order ID: '.$orderDetails['id'].'</div>
                            <div class="date">
                                Order Date: '.date('Y-m-d h:i:s', strtotime($orderDetails['created_at'])).'<br>
                                Order Amount: PHP '.$orderDetails['grand_total'].'<br>
                                Order Status: '.$orderDetails['order_status'].'<br>
                                Payment Method: '.$orderDetails['payment_method'].'<br>
                            </div>
                        </div>
                    </div>
        
                    <table border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th class="desc">Product Code</th>
                                <th class="qty">Size</th>
                                <th class="qty">Color</th>
                                <th class="qty">Quantity</th>
                                <th class="unit">Unit price</th>
                                <th class="total">Total</th>
                            </tr>
                        </thead>
                        <tbody>';
                        $subTotal = 0;
                        foreach($orderDetails['orders_products'] as $product){
                            $invoiceHTML .= '<tr>
                                <td class="desc">'.$product['product_code'].'</td>
                                <td class="qty">'.$product['product_size'].'</td>
                                <td class="qty">'.$product['product_color'].'</td>
                                <td class="qty">'.$product['product_qty'].'</td>
                                <td class="unit">PHP '.$product['product_price'].'</td>
                                <td class="total">PHP '.$product['product_price']*$product['product_qty'].'</td>
                            </tr>';
                            $subTotal = $subTotal + ($product['product_price']*$product['product_qty']);
                        }
        
                        $invoiceHTML .= '</tbody>
                    </table>
                    <div class="no-break">
                        <table class="grand-total">
                            <tbody>
                                <tr>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="total" colspan=2>SUBTOTAL</td>
                                    <td class="total">PHP '.$subTotal.'</td>
                                </tr>
                                <tr>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="total" colspan=2>SHIPPING</td>
                                    <td class="total">PHP 0</td>
                                </tr>
                                <tr>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="total" colspan=2>DISCOUNT</td>';
                                    if($orderDetails['coupon_amount']>0){
                                        $invoiceHTML .= '<td class="total">PHP '.$orderDetails['coupon_amount'].'</td>';
                                    }else{
                                        $invoiceHTML .= '<td class="total">PHP 0</td>';    
                                    }
                                $invoiceHTML .= '</tr>
                                <tr>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="total" colspan="2">TOTAL</td>
                                    <td class="total">PHP '.$orderDetails['grand_total'].'</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        
            <footer>
                <div class="container">
                    <div class="thanks">Thank you!</div>
                </div>
            </footer>
        
        </body>
        
        </html>
';        

        // Instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($invoiceHTML);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream();
    }


}
