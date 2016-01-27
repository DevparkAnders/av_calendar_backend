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
            $roles = [
                ['name' => RoleType::ADMIN],
                ['name' => RoleType::DEALER],
                ['name' => RoleType::DEVELOPER],
                ['name' => RoleType::CLIENT],
            ];
            
            foreach ($roles as $role) {
                Role::create($role);
                $this->command->info("Role '{$role['name']}' has been created");
            }
        }
    }
}
