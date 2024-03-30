<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;
use Session;

class CurrencyController extends Controller
{
    // Display currencies
    public function currencies(){
        Session::put('page','currencies');
        $currencies = Currency::get();
        return view('admin.currencies.currencies')->with(compact('currencies'));
    }

    // Update currency status
    public function updateCurrencyStatus(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            if($data['status']=="Active"){
                $status = 0;
            }else{
                $status = 1;
            }
            Currency::where('id',$data['currency_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'currency_id'=>$data['currency_id']]);
        }
    }

    // Delete currency
    public function deleteCurrency($id){
        // Delete Currency
        Currency::where('id',$id)->delete();
        $message = "Currency has been deleted successfully!";
        return redirect()->back()->with('success_message',$message);
    }

    // Add and edit currency
    public function addEditCurrency(Request $request,$id=null){
        Session::put('page','currencies');
        if($id==""){
            $title = "Add Currency";
            $currency = new Currency;
            $message = "Currency added successfully!";
        }else{
            $title = "Edit Currency";
            $currency = Currency::find($id);
            $message = "Currency updated successfully!";
        }
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            // Enter Validation
            $rules = [
                'currency_code' => 'required|regex:/^[\pL\s\-]+$/u',
                'exchange_rate' => 'required|numeric|between:0,10000',
            ];
            // Custom error messages
            $customMessages = [
                'currency_code.required' => 'Currency Code is required',
                'currency_code.regex' => 'Valid Currency Code is required',
                'exchange_rate.required' => 'Exchange Rate is required',
                'exchange_rate.between' => 'Valid Exchange Rate is required',
            ];

            $this->validate($request,$rules,$customMessages);

            $currency->currency_code = $data['currency_code'];
            $currency->exchange_rate = $data['exchange_rate'];
            $currency->status = 1;
            $currency->save();

            return redirect('admin/currencies')->with('success_message',$message);
        }

        return view('admin.currencies.add_edit_currency')->with(compact('title','currency'));
    }
}
