<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Iyzipay;
use App\Models\Order;
use Session;

class IyzipayController extends Controller
{
    public function iyzipay(){
        if(Session::has('order_id')){
            return view('front.iyzipay.iyzipay');
        }else{
            return redirect('cart');
        }
    }

    public function pay(){

        $orderDetails = Order::with('orders_products')->where('id',Session::get('order_id'))->first()->toArray();
        /*dd($orderDetails);*/
        $nameArr = explode(' ',$orderDetails['name']);

        $options = Iyzipay::options();

        $request = new \Iyzipay\Request\CreatePayWithIyzicoInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId(Session::get('order_id'));
        $request->setPrice(Session::get('grand_total'));
        $request->setPaidPrice(Session::get('grand_total'));
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId(Session::get('order_id'));
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl("https://www.merchant.com/callback");
        $request->setEnabledInstallments(array(2, 3, 6, 9));
        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId($orderDetails['user_id']);
        $buyer->setName($nameArr[0]);
        $buyer->setSurname($nameArr[1]);
        $buyer->setGsmNumber("+905350000000");
        $buyer->setEmail($orderDetails['email']);
        $buyer->setIdentityNumber("74300864791");
        $buyer->setLastLoginDate("");
        $buyer->setRegistrationDate("");
        $buyer->setRegistrationAddress($orderDetails['address']);
        $buyer->setIp("");
        $buyer->setCity($orderDetails['city']);
        $buyer->setCountry($orderDetails['country']);
        $buyer->setZipCode($orderDetails['pincode']);
        $request->setBuyer($buyer);
        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName($orderDetails['name']);
        $shippingAddress->setCity($orderDetails['city']);
        $shippingAddress->setCountry($orderDetails['country']);
        $shippingAddress->setAddress($orderDetails['address']);
        $shippingAddress->setZipCode($orderDetails['pincode']);
        $request->setShippingAddress($shippingAddress);
        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName($orderDetails['name']);
        $billingAddress->setCity($orderDetails['city']);
        $billingAddress->setCountry($orderDetails['country']);
        $billingAddress->setAddress($orderDetails['address']);
        $billingAddress->setZipCode($orderDetails['pincode']);
        $request->setBillingAddress($billingAddress);
        $basketItems = array();
        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId(Session::get('order_id'));
        $firstBasketItem->setName("Order ID: ".Session::get('order_id'));
        $firstBasketItem->setCategory1("Stack Developers Products");
        $firstBasketItem->setCategory2("");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice(Session::get('grand_total'));
        $basketItems[0] = $firstBasketItem;
        /*$secondBasketItem = new \Iyzipay\Model\BasketItem();
        $secondBasketItem->setId("BI102");
        $secondBasketItem->setName("Game code");
        $secondBasketItem->setCategory1("Game");
        $secondBasketItem->setCategory2("Online Game Items");
        $secondBasketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
        $secondBasketItem->setPrice("0.5");
        $basketItems[1] = $secondBasketItem;
        $thirdBasketItem = new \Iyzipay\Model\BasketItem();
        $thirdBasketItem->setId("BI103");
        $thirdBasketItem->setName("Usb");
        $thirdBasketItem->setCategory1("Electronics");
        $thirdBasketItem->setCategory2("Usb / Cable");
        $thirdBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $thirdBasketItem->setPrice("0.2");
        $basketItems[2] = $thirdBasketItem;*/
        $request->setBasketItems($basketItems);
        # make request
        $payWithIyzicoInitialize = \Iyzipay\Model\PayWithIyzicoInitialize::create($request, $options);

        $paymentResponse = (array)$payWithIyzicoInitialize;

        /*echo "<pre>"; print_r($paymentResponse); die;*/

        foreach ($paymentResponse as $key => $response) {
            $response_decode = json_decode($response);
            /*echo "<pre>"; print_r($response_decode); die;*/
            $pay_url = $response_decode->payWithIyzicoPageUrl;
            break;
        }

        return redirect($pay_url);

    }
}
