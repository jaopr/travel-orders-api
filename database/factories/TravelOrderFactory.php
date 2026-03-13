<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelOrderFactory extends Factory {
    
    public function definition(): array {
        $departure = $this->faker->dateTimeBetween('+1 week', '+1 month');
        $return = $this->faker->dateTimeBetween($departure, '+2 months');

        return [
            'user_id' => User::factory(),
            'requester_name' => $this->faker->name(),
            'destination' => $this->faker->city(),
            'departure_date' => $departure->format('Y-m-d'),
            'return_date' => $return->format('Y-m-d'),
            'status' => 'requested',
        ];
    }
}