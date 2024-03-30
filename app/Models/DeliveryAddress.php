<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class DeliveryAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','name','address','city','barangay','country','pincode','status','mobile'
    ];

    public static function deliveryAddresses(){
        $deliveryAddresses = DeliveryAddress::where('user_id',Auth::user()->id)->get()->toArray();
        return $deliveryAddresses;
    }
}
