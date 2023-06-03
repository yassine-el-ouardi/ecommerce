<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status', 'order_method', 'user_id', 'voucher_id', 'payment_done', 'order',
        'payment_token', 'currency', 'total_amount', 'user_address_id'
    ];


    public function address()
    {
        return $this->hasOne(UserAddress::class, 'id', 'user_address_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function user_info()
    {
        return $this->hasOne(User::class, 'id', 'user_id')
            ->select(['id', 'name', 'email']);
    }

    public function voucher()
    {
        return $this->hasOne(Voucher::class, 'id', 'voucher_id');
    }

    public function ordered_p()
    {
        return $this->hasMany(OrderedProduct::class, 'order_id', 'id');
    }


    public function ordered_products()
    {
        return $this->hasMany(OrderedProduct::class, 'order_id', 'id')
            ->select([ 'product_id', 'inventory_id', 'quantity', 'shipping_place_id', 'shipping_type',
                'selling', 'shipping_price', 'tax_price', 'bundle_offer', 'order_id']);
    }

    public function cancellation()
    {
        return $this->hasOne(Cancellation::class, 'order_id', 'id');
    }

    public function ordered_price()
    {
        return $this->hasOne(OrderedProduct::class)
            ->selectRaw('ordered_products.order_id, SUM(ordered_products.selling) as total')
            ->groupBy('ordered_products.order_id');
    }
}
