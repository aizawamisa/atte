<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Work;
use App\Models\User;

class WorkFactory extends Factory
{
    protected $model = Work::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dummyDate = $this->faker->dateTimeThisMonth;

        return [
            'start_work' => $dummyDate->format('Y-m-d H:i:s'),
            'end_work' => $dummyDate->modify('+9 hours')->format('Y-m-d H:i:s'),
            'user_id' =>  User::factory(),
        ];
    }
}

// class WorkFactory extends Factory
// {
//     protected $model = Work::class;
//     /**
//      * Define the model's default state.
//      *
//      * @return array
//      */
//     public function definition()
//     {
//         $dummyDate = $this->faker->dateTimeThisMonth;
        
//         return [
//             'user_id' =>  User::factory(),
//             'start_work' => $dummyDate->format('Y-m-d H:i:s'),
//             'end_work' => $dummyDate->modify('+9 hours')->format('Y-m-d H:i:s'),
//         ];
//     }

//     public function configure()
//     {
//         return $this->afterCreating(function (Work $work){
//             $work->user()->associate(User::inRandomOrder()->first())->save();
//         });
//     }

//     public function forUser($userId)
//     {
//         return $this->state([
//             'user_id' => $userId,
//         ]);
//     }
// }
