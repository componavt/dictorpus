<?php

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
//        $this->call(RoleSeeder::class);
        $this->call(DmarkerSeeder::class);
        $this->command->info('Dmarker table seeded!');
        $this->call(MvariantSeeder::class);
        $this->command->info('Mvariant table seeded!');
    }
}
