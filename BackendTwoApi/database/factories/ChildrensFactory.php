<?php

namespace Database\Factories;

use App\Models\Childrens;
use App\Models\Parents;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChildrensFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Childrens::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'parent_id' => 1,
            'name'=> $this->faker->randomElement(['สินทรัพย์','สภาพทาง']),
            'route' => $this->faker->randomElement(['/dashboards/asset','/dashboards/condition']),
        ];
    }
}
