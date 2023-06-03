<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class ProductCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'status', 'admin_id'
    ];

    protected $hidden = [
        'admin_id'
    ];

    public function product_collections()
    {
        return $this->hasMany(CollectionWithProduct::class, 'product_collection_id', 'id');
    }



}
