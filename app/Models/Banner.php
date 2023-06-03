<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'image', 'source_type', 'tags', 'url', 'title', 'status', 'admin_id', 'closable', 'type'
    ];

    protected $hidden = [
        'admin_id'
    ];

    public function source_brands()
    {
        return $this->hasMany(BannerSourceBrand::class, 'banner_id', 'id');
    }

    public function source_categories()
    {
        return $this->hasMany(BannerSourceCategory::class, 'banner_id', 'id');
    }

    public function source_sub_categories()
    {
        return $this->hasMany(BannerSourceSubCategory::class, 'banner_id', 'id');
    }

    public function source_products()
    {
        return $this->hasMany(BannerSourceProduct::class, 'banner_id', 'id');
    }

}
