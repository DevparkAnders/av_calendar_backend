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
        $this->call(\App\Modules\User\Database\Seeds\UserDatabaseSeeder::class);

        // we don't use it for now, because we want to run seeders in custom order
        // SimpleModule::seed($this);
    }
}
