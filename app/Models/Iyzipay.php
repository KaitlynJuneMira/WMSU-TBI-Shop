<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iyzipay extends Model
{
    use HasFactory;

    public static function options()
    {
        $options = new \Iyzipay\Options();
        $options->setApiKey('sandbox-W7IiunBL5OALo4iibT3r0S3t3fMswzkn');
        $options->setSecretKey('sandbox-gVf4cjziwu6FJGrwkeIyBlPlizniaqhw');
        $options->setBaseUrl('https://sandbox-api.iyzipay.com');

        return $options;
    }
}
