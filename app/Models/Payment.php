<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'cash_on_delivery', 'razorpay_key', 'razorpay_secret', 'stripe_key', 'stripe_secret',
        'paypal', 'paypal_key', 'paypal_secret','admin_id',
        'razorpay', 'stripe',
        'flutterwave', 'fw_environment','fw_public_key', 'fw_secret_key', 'fw_encryption_key',

    ];

    protected $hidden = [
        'admin_id'
    ];

    use HasFactory;
}
