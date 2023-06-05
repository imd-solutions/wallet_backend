<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
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
            'amount' => $this->faker->randomFloat() ,
            'payment_method' => $this->faker->text ,
            'status' => $this->faker->boolean,
            'reference' => $this->faker->text,
        ];
    }
}
