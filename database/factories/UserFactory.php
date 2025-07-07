<?php

namespace Database\Factories;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = User::class;

    // Các biến static này chỉ khởi tạo 1 lần duy nhất khi factory chạy, giúp tránh lặp lại việc lấy và loại các giá trị từ config
    protected static $restPositions;
    protected static $restDepartments;
    protected static $restRoles;

    public function definition(): array
    {
        if (!self::$restRoles) {
            self::$restRoles = array_values(Arr::except(config('roles'), ['super-admin', 'admin', 'client'])); // khong lay super-admin', 'admin, client
        }
        if (!self::$restPositions) {
            self::$restPositions = array_values(Arr::except(config('positions'), ['administration', 'ceo', 'secretary_to_ceo'])); // khong lay ['administration', 'ceo','secretary_to_ceo']
        }
        if (!self::$restDepartments) {
            self::$restDepartments = array_values(Arr::except(config('departments'), ['management'])); // khong lay ['administration', 'ceo','secretary_to_ceo']
        }

        $name = $this->faker->unique()->name;
        $email = str_replace('-', '', Str::slug($name)) . '@example.com';
        return [
            'name' => $name,
            'email' => $email,
            'role' => $this->faker->randomElement(self::$restRoles),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'position' => $this->faker->randomElement(self::$restPositions),
            'department' => $this->faker->randomElement(self::$restDepartments),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ];
    }

    public function addSuperAdmin()
    {
        return $this->state([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'role' => config('roles.super_admin'),
            'status' => 'active',
            'position' => config('positions.admin'),
            'department' => config('departments.management'),
            'email_verified_at' => now(),
            'password' => Hash::make('superadmin12345'),
        ]);
    }

    public function addAdmin(string $name)
    {
        $email = str_replace('-', '', Str::slug($name)) . uniqid() . '@example.com';
        return $this->state([
            'name' => $name,
            'email' =>  $email,
            'role' => config('roles.admin'),
            'status' => 'active',
            'position' => config('positions.admin'),
            'department' => config('departments.management'),
            'email_verified_at' => now(),
            'password' => Hash::make('admin12345'),
        ]);
    }

    public function addCEO()
    {
        return $this->state([
            'name' => 'CEO',
            'email' => 'ceo@example.com',
            'role' => config('roles.admin'),
            'status' => 'active',
            'position' => config('positions.ceo'),
            'department' => config('departments.management'),
            'email_verified_at' => now(),
            'password' => Hash::make('ceo12345'),
        ]);
    }

    public function addSecretaryCEO()
    {
        return $this->state([
            'name' => 'Secretary',
            'email' => 'secretary@example.com',
            'role' => config('roles.admin'),
            'status' => 'active',
            'position' => config('positions.secretary_to_ceo'),
            'department' => config('departments.management'),
            'email_verified_at' => now(),
            'password' => Hash::make('secretary12345'),
        ]);
    }

    public function addClient(): self
    {
        return $this->state(function () {
            $name = $this->faker->unique()->name;
            $email = str_replace('-', '', Str::slug($name)) . '@example.com';
            return [
                'name' => $name,
                'email' => $email,
                'role' => config('roles.client'),
                'status' => $this->faker->randomElement(['active', 'inactive']),
                'position' => null,
                'department' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('client12345'),
            ];
        });
    }
}
