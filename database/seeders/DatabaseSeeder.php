<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	 $userQuantity = 45;
    	 
    	 \App\Models\User::truncate();

         \App\Models\User::flushEventListeners();
         
         \App\Models\User::factory($userQuantity)->create();
    }
}
