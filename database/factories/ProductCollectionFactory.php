<?php

namespace Database\Factories;

use App\Models\ProductCollection;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCollectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductCollection::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->name,
            'status' => 1,
            'admin_id' => 1
        ];
    }
}
