<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersLog extends Model
{
    use HasFactory;

    // Get order products
    public function orders_products(){
        return $this->hasMany('App\Models\OrdersProduct','id','order_item_id');
    }

    // Get specific order details
    public static function getItemDetails($order_item_id){
        $getItemDetails = OrdersProduct::where('id',$order_item_id)->first()->toArray();
        return $getItemDetails;
    }
}
