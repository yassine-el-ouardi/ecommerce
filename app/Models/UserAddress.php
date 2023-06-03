<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'country', 'state', 'city', 'zip', 'address_1', 'address_2' , 'user_id', 'name', 'phone',
        'default', 'delivery_instruction'
    ];

    protected $hidden = [
        'created_at',  'updated_at'
    ];

}
