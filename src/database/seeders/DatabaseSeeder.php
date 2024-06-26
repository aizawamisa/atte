<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Work;
use App\Models\Rest;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(15)->create()->each(function ($user)
        {
            Work::factory(10)->create(['user_id' => $user->id])->each(function ($work) {
                Rest::factory(3)->create(['work_id' => $work->id]);
            });
        });
    }
}

// class DatabaseSeeder extends Seeder
// {
//     /**
//      * Seed the application's database.
//      *
//      * @return void
//      */
//     public function run()
//     {
//         User::factory(10)->create()->each(function ($user)
//         {
//             Work::factory(5)->forUser($user->id)->create()->each(function ($work) {
//                 Rest::factory(3)->forWork($work->id)->create();
//             });
//         });
//     }
// }
