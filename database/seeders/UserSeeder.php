<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin, CEO, Secretary
        User::factory()->addSuperAdmin()->create();
        User::factory()->addCEO()->create();

        // Admins
        foreach (['Admin A', 'Admin B', 'Admin C'] as $adminName) {
            User::factory()->addAdmin($adminName)->create();
        }

        // Departments (ngoại trừ 'management' - chỉ dùng cho admin/superadmin/ceo)
        $departments = array_values(array_diff(config('departments'), [config('departments.management')]));

        
        foreach ($departments as $department) {
            // Managers (tong 5)
            User::factory()->addManager($department)->create();

            // Leaders: mỗi department 2 leader (tong 10)
            User::factory()->count(2)->addLeader($department)->create();

            // member: mỗi department 10 member (tong 50)
            User::factory()->count(10)->addMember($department)->create();
        }

        // Clients
        User::factory()->count(30)->addClient()->create();
    }
}
