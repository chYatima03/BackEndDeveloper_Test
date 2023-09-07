<?php

namespace Database\Seeders;

use App\Models\Childrens;
use App\Models\Parents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        Parents::factory()
            ->count(1)
            ->create();
        Childrens::factory()
            ->count(2)
            ->create();
    }
}
