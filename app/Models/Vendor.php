<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    public function vendorbusinessdetails(){
        return $this->belongsTo('App\Models\VendorsBusinessDetail','id','vendor_id');
    }

    public static function getVendorShop($vendorid){
        $getVendorShop = VendorsBusinessDetail::select('shop_name')->where('vendor_id',$vendorid)->first()->toArray();
        return $getVendorShop['shop_name'];
    }

    public static function getVendorCommission($vendorid){
        $vendorCommissionCount = Vendor::where('id',$vendorid)->count();
        if($vendorCommissionCount>0){
            $getVendorCommission = Vendor::select('commission')->where('id',$vendorid)->first()->toArray();
            $vendorComission = $getVendorCommission['commission'];    
        }else{
            $vendorComission = 0;
        }
        
        return $vendorComission;
    }
}
