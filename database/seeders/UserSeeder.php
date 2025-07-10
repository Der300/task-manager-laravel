<?php

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        
        User::factory()->addSuperAdmin()->create(); //tao super-admin

        User::factory()->addCEO()->create();        //tao ceo

        User::factory()->addSecretaryCEO()->create();   //tao secretary

        User::factory()->addAdmin('Admin A')->create();   //tao admin
        User::factory()->addAdmin('Admin B')->create();   //tao admin

        User::factory()->count(95)->addNormalUser()->create();       //tao user binh thuong
        
        User::factory()->count(50)->addClient()->create();       //tao client binh thuong



    }
}
