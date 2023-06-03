<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'user_id', 'rating', 'review', 'order_id'
    ];

    protected $hidden = [
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')
            ->select(['id', 'title']);
    }

    public function product_admin()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')
            ->select(['id', 'admin_id']);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function review_images()
    {
        return $this->hasMany(ReviewImage::class, 'rating_review_id', 'id');
    }
}
