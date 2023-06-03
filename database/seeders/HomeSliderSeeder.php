<?php

namespace Database\Seeders;

use App\Models\HomeSlider;
use App\Models\HomeSliderSourceBrand;
use App\Models\HomeSliderSourceCategory;
use App\Models\HomeSliderSourceSubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class HomeSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $homeSliders = [
            [
                'id' => 1,
                'type' => Config::get('constants.homeSlider.MAIN'),
                'image' => 'slider-1.webp',
                'title' => 'Winter sale',
                'source_type' => Config::get('constants.sliderSourceType.CATEGORY'),
                'status' => Config::get('constants.status.PUBLIC'),
                'admin_id' => 1
            ],
            [
                'id' => 2,
                'type' => Config::get('constants.homeSlider.MAIN'),
                'image' => 'slider-2.webp',
                'title' => 'Flash 50 % off',
                'source_type' => Config::get('constants.sliderSourceType.CATEGORY'),
                'status' => Config::get('constants.status.PUBLIC'),
                'admin_id' => 1
            ],
            [
                'id' => 3,
                'type' => Config::get('constants.homeSlider.MAIN'),
                'image' => 'slider-3.webp',
                'title' => 'Black Friday Discount',
                'source_type' => Config::get('constants.sliderSourceType.CATEGORY'),
                'status' => Config::get('constants.status.PUBLIC'),
                'admin_id' => 1
            ],
            [
                'id' => 4,
                'type' => Config::get('constants.homeSlider.RIGHT_TOP'),
                'image' => 'slider-4.webp',
                'title' => 'Backpack for Men',
                'source_type' => Config::get('constants.sliderSourceType.SUB_CATEGORY'),
                'status' => Config::get('constants.status.PUBLIC'),
                'admin_id' => 1
            ],
            [
                'id' => 5,
                'type' => Config::get('constants.homeSlider.RIGHT_BOTTOM'),
                'image' => 'slider-5.webp',
                'title' => 'Puma Stylist Shoes',
                'source_type' => Config::get('constants.sliderSourceType.BRAND'),
                'status' => Config::get('constants.status.PUBLIC'),
                'admin_id' => 1
            ]
        ];

        foreach ($homeSliders as $i) {
            HomeSlider::create($i);
        }

        // Source seeders for main slider image
        $categorySource = [
            [
                'category_id' => 63082111,
                'home_slider_id' => 1
            ],
            [
                'category_id' => 63082112,
                'home_slider_id' => 1
            ],
            [
                'category_id' => 72531153,
                'home_slider_id' => 1
            ],
            [
                'category_id' => 61952111,
                'home_slider_id' => 2
            ],
            [
                'category_id' => 72531153,
                'home_slider_id' => 2
            ],
            [
                'category_id' => 63082111,
                'home_slider_id' => 3
            ],
            [
                'category_id' => 61952111,
                'home_slider_id' => 3
            ]
        ];
        foreach ($categorySource as $i) {
            HomeSliderSourceCategory::create($i);
        }

        // Source seeders for slider image right top
        $subCategorySource = [
            [
                'sub_category_id' => 97373117,
                'home_slider_id' => 4
            ],
            [
                'sub_category_id' => 73294112,
                'home_slider_id' => 4
            ],
            [
                'sub_category_id' => 96765129,
                'home_slider_id' => 4
            ],
            [
                'sub_category_id' => 96765126,
                'home_slider_id' => 4
            ]
        ];
        foreach ($subCategorySource as $i) {
            HomeSliderSourceSubCategory::create($i);
        }

        // Source seeders for slider image right top
        $bandSource = [
            [
                'brand_id' => 9442200,
                'home_slider_id' => 5
            ],
            [
                'brand_id' => 9442201,
                'home_slider_id' => 5
            ],
            [
                'brand_id' => 9442202,
                'home_slider_id' => 5
            ],
            [
                'brand_id' => 9442203,
                'home_slider_id' => 5
            ]
        ];
        foreach ($bandSource as $i) {
            HomeSliderSourceBrand::create($i);
        }
    }
}
