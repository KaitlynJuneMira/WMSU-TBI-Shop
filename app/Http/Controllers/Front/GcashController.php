<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\Mail;
use Auth;
use Ixudra\Curl\Facades\Curl;
use App\Models\ProductsAttribute;

class GcashController extends Controller
{
    public function pay(Request $request)
        {
            if(!Session::has('order_id')){
                return redirect('cart');
            }

            $order_id = Session::get('order_id');

            // Get the ordered product from DB
            $order = Order::with('orders_products')->where('id', $order_id)->first();

            // Retrieve the products ordered one by 1 and insert to array
            $lineItems = [];
            foreach ($order->orders_products as $orderProduct) {
                $lineItems[] = [
                    'currency' => 'PHP',
                    'amount' => round($orderProduct->product_price * 100),
                    'name' => $orderProduct->product_name . ' ' . $orderProduct->product_size . '/' . $orderProduct->product_color,
                    'quantity' => $orderProduct->product_qty,
                ];
            }

            $data = [
                // Data for payments
                'data' => [
                    // Attributes for payment
                    'attributes' => [
                        // Billing Details
                        'billing' => [
                            'name' => Auth::user()->name,
                            'email' => Auth::user()->email,
                            'phone' => Auth::user()->mobile,
                        ],
                        // Line of Items Ordered
                        'line_items' => $lineItems,
                        // Different Type of Payment Method
                        'payment_method_types' => [
                            'gcash', 'billease', 'card', 'dob', 'dob_ubp', 'grab_pay', 'paymaya'
                        ],
                        'success_url' => 'http://wmsutbiu.shop/payment/success',
                        'cancel_url' => 'http://wmsutbiu.shop/checkout',
                        'description'   => 'WMSU-TBI Payment',
                        'send_email_receipt' => true,
                    ],
                    
                ]
            ];
            // Send a checkout request to paymongo
            $response = Curl::to('https://api.paymongo.com/v1/checkout_sessions')
            ->withHeader('Content-Type: application/json')
            ->withHeader('accept: application/json')
            ->withHeader('Authorization: Basic ' . env('PAYMONGO_SECRET_KEY'))
            ->withData($data)
            ->asJson()
            ->post();
            // Make a session ID for payment  
            session(['paymongo_session' =>  $response->data->id]);
            // Redirect the user to checkout page
            
            return redirect()->to($response->data->attributes->checkout_url);

        }

    public function success(Request $request)
        {
            // Perform any actions you need after successful payment
            // For example, update order status, send confirmation emails, etc.
            // You can retrieve any necessary information from the request or session

            if (!Session::has('order_id')) {
                return redirect('cart');
            }
            // Retrieve the paymongo session
            $sessionId = session('paymongo_session');
            // Fetch the payment Data
            $response = Curl::to('https://api.paymongo.com/v1/checkout_sessions/'. $sessionId)
            ->withHeader('accept: application/json')
            ->withHeader('Authorization: Basic ' . env('PAYMONGO_SECRET_KEY'))
            ->asJson()
            ->get();
            // Fetch the payment details
            $payments = $response->data->attributes->payments;

            foreach ($payments as $payment) {
                $paymongo_id = $payment->id;
    
                $attributes = $payment->attributes;
    
                $payment_status = $attributes->status;
                $payer_id = $attributes->balance_transaction_id;
                $currency = $attributes->currency;
                $amount = $attributes->amount;
            }
            // Check if there's a payment
            if(!$paymongo_id){
                return redirect('cart');
            }
            else {
                // Check if the existing payment already exist
                $existingPayment = Payment::where('payment_id', $paymongo_id)->first();
                if ($existingPayment) {
                    return view('front.gcash.success');
                }
                // Add New Payment
                $payment = new Payment;
                $payment->order_id = Session::get('order_id');
                $payment->user_id = Auth::user()->id;
                $payment->payment_id = $paymongo_id;
                $payment->payer_id = $payer_id;
                $payment->payer_email = $response->data->attributes->billing->email;
                $payment->amount = $amount / 100;
                $payment->currency = $currency;
                $payment->payment_status = ucfirst($payment_status);
                $payment->save();
    
                /*return "Payment is Successful. Your transaction is ". $arr['id'];*/
    
                // Update the Order
                $order_id = Session::get('order_id');
    
                // Update Order Status to Paid
                Order::where('id',$order_id)->update(['order_status'=>'Paid']);
    
                // Send Order SMS
                /* $message = "Dear Customer, your order ".$order_id." has been successfully placed with StackDevelopers.in. We will intimate you once your order is shipped.";
                $mobile = Auth::user()->mobile;
                Sms:sendSms($message,$mobile); */
    
                $orderDetails = Order::with('orders_products')->where('id',$order_id)->first()->toArray();
    
                // Send Order Email
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
    
                // Reduce Stock Script Starts
                foreach ($orderDetails['orders_products'] as $key => $order) {
                    $getProductStock = ProductsAttribute::getProductStock($order['product_id'],$order['product_size']);
                    $newStock = $getProductStock - $order['product_qty'];
                    ProductsAttribute::where(['product_id'=>$order['product_id'],'size'=>$order['product_size']])->update(['stock'=>$newStock]);
                }
                // Reduce Stock Script Ends
    
                // Empty the cart
                Cart::where('user_id',Auth::user()->id)->delete();
    
                // Redirect the user to a "success" page
                return redirect()->route('payment.success');
            }
        }

        public function thanks(Request $request)
        {
            // View the success page or cart if there's none
            if(Session::has('order_id')){
                return view('front.gcash.success');
            } else{
                return redirect('cart');
            }
        }
}