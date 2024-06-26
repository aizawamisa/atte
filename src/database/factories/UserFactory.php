<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'status' => $this->faker->numberBetween(1, 3),
        ];
    }
}

// class UserFactory extends Factory
// {
//     /**
//      * Define the model's default state.
//      *
//      * @return array
//      */
//     public function definition()
//     {
//         return [
//             'name' => $this->faker->name(),
//             'email' => $this->faker->unique()->safeEmail(),
//             'email_verified_at' => now(),
//             'password' => Hash::make('password'),
//             ];
//     }
// }

// class UserFactory extends Factory
// {
//     /**
//      * Define the model's default state.
//      *
//      * @return array
//      */
//     public function definition()
//     {
//         return [
//             'name' => $this->faker->name(),
//             'email' => $this->faker->unique()->safeEmail(),
//             'email_verified_at' => now(),
//             'password' => Hash::make('password'),
//             ];
//     }
// }

