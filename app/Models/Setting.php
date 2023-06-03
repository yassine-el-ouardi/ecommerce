<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency', 'currency_icon', 'currency_position', 'phone', 'address_1' , 'email',
        'address_2', 'city', 'state', 'zip', 'country','admin_id'
    ];

    protected $hidden = [
        'admin_id'
    ];
}
