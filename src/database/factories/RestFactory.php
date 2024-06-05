<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rest;
use App\Models\Work;

class RestFactory extends Factory
{
    protected $model = Rest::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dummyDate = $this->faker->dateTimeThisMonth;
        
        return [
            'work_id' =>  Work::factory(),
            'start_rest' => $dummyDate->format('Y-m-d H:i:s'),
            'end_rest' => $dummyDate->modify('+1 hours')->format('Y-m-d H:i:s'),
        ];
    }

    public function forWork($workId)
    {
        return $this->state([
            'work_id' => $workId,
        ]);
    }
}
