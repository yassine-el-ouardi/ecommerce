<?php

namespace Database\Seeders;

use App\Models\ReviewImage;
use Illuminate\Database\Seeder;

class ReviewImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [

                'rating_review_id' => 1,
                'image' => 'review-1.webp'
            ],
            [
                'rating_review_id' => 1,
                'image' => 'review-2.webp'
            ],
            [
                'rating_review_id' => 2,
                'image' => 'review-3.webp'
            ],
            [
                'rating_review_id' => 2,
                'image' => 'review-4.webp'
            ],
            [
                'rating_review_id' => 2,
                'image' => 'review-5.webp'
            ],
            [
                'rating_review_id' => 3,
                'image' => 'review-6.webp'
            ],
            [
                'rating_review_id' => 3,
                'image' => 'review-7.webp'
            ],
            [
                'rating_review_id' => 4,
                'image' => 'review-8.webp'
            ], [
                'rating_review_id' => 5,
                'image' => 'review-9.webp'
            ],
            [
                'rating_review_id' => 5,
                'image' => 'review-10.webp'
            ],
            [
                'rating_review_id' => 6,
                'image' => 'review-11.webp'
            ],
            [
                'rating_review_id' => 6,
                'image' => 'review-12.webp'
            ],
            [
                'rating_review_id' => 7,
                'image' => 'review-13.webp'
            ],
            [
                'rating_review_id' => 8,
                'image' => 'review-14.webp'
            ],
            [
                'rating_review_id' => 9,
                'image' => 'review-15.webp'
            ]
        ];

        foreach ($items as $i) {
            ReviewImage::create($i);
        }
    }
}
