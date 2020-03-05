<?php

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
        $this->call(MusicGenresTableSeeder::class);
        $this->call(MusicInstrumentsTableSeeder::class);
        $this->call(MusicSkillsTableSeeder::class);
    }
}
