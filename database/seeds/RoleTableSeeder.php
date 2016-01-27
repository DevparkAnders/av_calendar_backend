<?php

use App\Models\Role;
use App\Models\RoleType;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Role::count()) {
            foreach (RoleType::all() as $type) {
                Role::create(['name' => $type]);
                $this->command->info("Role '{$type}' has been created");
            }
        }
    }
}
