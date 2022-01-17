<?php

namespace Database\Factories;

use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrdereFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $link = Link::inRandomOrder()->first();
        
        return [
            'link_code' => $link->code,
            'link_id' => $link->id,
            'user_id' => $link->user->id,
            'link_ambassador_email' => $link->user->email,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'is_completed' => true
        ];
    }
}
