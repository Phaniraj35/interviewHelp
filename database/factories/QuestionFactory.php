<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'question' => $this->faker->sentence(),
            'answer' => $this->faker->sentence(),
            'difficulty' => array_rand([0,1,2]),
            'user_id' => User::factory()->create()->id,
            'category_id' => Category::factory()->create()->id
        ];
    }
}
