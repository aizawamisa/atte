<?php

namespace Database\Seeders;

use App\Models\Rest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rest::factory()->count(500)->create;
    }
}
