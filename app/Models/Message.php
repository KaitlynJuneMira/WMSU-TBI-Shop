<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'message'; 

    protected $fillable = [
        'user_id', 'vendor_id', 'message', 'status',  'service','video_proof'
    ];
}

