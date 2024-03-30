<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingCharge;
use Session;

class ShippingController extends Controller
{
    // Display shipping charrges
    public function shippingCharges(){
        Session::put('page','shipping');
        $shippingCharges = ShippingCharge::get()->toArray();
        //dd($shippingCharges);
        return view('admin.shipping.shipping_charges')->with(compact('shippingCharges'));
    }

    // Uodate shipping status
    public function updateShippingStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }
            ShippingCharge::where('id',$data['shipping_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'shipping_id'=>$data['shipping_id']]);
        }
    }

    // Edit shipping charges
    public function editShippingCharges($id, Request $request){
        Session::put('page','shipping');
        if($request->isMethod('post')){
            $data = $request->all();
            ShippingCharge::where('id',$id)->update(['0_500g'=>$data['0_500g'],'501_1000g'=>$data['501_1000g'],'1001_2000g'=>$data['1001_2000g'],'2001_5000g'=>$data['2001_5000g'],'above_5000g'=>$data['above_5000g']]);
            $message = "Shipping Charges updated successfully!";
            return redirect()->back()->with('success_message',$message);
        }

        $shippingDetails = ShippingCharge::where('id',$id)->first();
        $title = "Edit Shipping Charges";
        return view('admin.shipping.edit_shipping_charges')->with(compact('shippingDetails','title'));
    }
}
