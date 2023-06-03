<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cancellation extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'title', 'message', 'refunded'
    ];

    use HasFactory;
}
