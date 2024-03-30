<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $table = 'reply'; 

    protected $fillable = [
        'message_id', 'sender_id', 'receiver_id', 'message',  'image'
    ];
}

