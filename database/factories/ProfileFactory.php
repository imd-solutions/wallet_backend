<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'balance' => $this->faker->randomFloat()
        ];
    }
}
