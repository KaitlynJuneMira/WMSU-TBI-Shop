<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryAddress;
use App\Models\Country;
use App\Models\Barangay;
use Auth;
use Validator;

class AddressController extends Controller
{
    // Fetch delivery address
    public function getDeliveryAddress(Request $request){
        if($request->ajax()){
            $data = $request->all();
            $deliveryAddress = DeliveryAddress::where('id',$data['addressid'])->first()->toArray();
            return response()->json(['address'=>$deliveryAddress]);
        }
    }

    // Save delivery address
    public function saveDeliveryAddress(Request $request){
        if($request->ajax()){
            // Enter validation
            $validator = Validator::make($request->all(),[
                'delivery_name' => ['required', 'string', 'max:100', 'regex:/^\b\w+\s\w+(\s\w+)?\b$/'],
                'delivery_mobile' => ['required', 'numeric', 'digits:11', 'regex:/^09\d{9}$/'],                
                'delivery_address'=>'required|string|max:100',
                'delivery_city'=>'required|string|max:100',
                'delivery_barangay'=>'required|string|max:100',
                'delivery_country'=>'required|string|max:100',
                'delivery_pincode'=>'required|digits:4',
            ]);
            // Get address infomations
            if($validator->passes()){
                $data = $request->all();
                //echo "<pre>"; print_r($data); die;
                $address = array();
                $address['user_id']=Auth::user()->id;
                $address['name']=$data['delivery_name'];
                $address['address']=$data['delivery_address'];
                $address['city']=$data['delivery_city'];
                $address['barangay']=$data['delivery_barangay'];
                $address['country']=$data['delivery_country'];
                $address['pincode']=$data['delivery_pincode'];
                $address['mobile']=$data['delivery_mobile'];
                if(!empty($data['delivery_id'])){
                    // Edit delivery address
                    DeliveryAddress::where('id',$data['delivery_id'])->update($address);
                }else{
                    /*$address['status']=1;*/
                    // Add delivery address
                    DeliveryAddress::create($address);
                }
                $deliveryAddresses = DeliveryAddress::deliveryAddresses();
                $countries = Country::where('status',1)->get()->toArray();
                $barangay = Barangay::all()->toArray();
                return response()->json([
                    'view'=>(String)View::make('front.products.delivery_addresses')->with(compact('deliveryAddresses','countries','barangay'))
                ]);
            }else{
                return response()->json(['type'=>'error','errors'=>$validator->messages()]);
            }
            
        }
    }

    // Remove delivery address
    public function removeDeliveryAddress(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            DeliveryAddress::where('id',$data['addressid'])->delete();
            $deliveryAddresses = DeliveryAddress::deliveryAddresses();
            $countries = Country::where('status',1)->get()->toArray();
            $barangay = Barangay::all()->toArray();
            return response()->json([
                'view'=>(String)View::make('front.products.delivery_addresses')->with(compact('deliveryAddresses','countries','barangay'))
            ]);
        }
    }
}
