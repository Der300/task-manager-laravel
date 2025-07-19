<?php

namespace Database\Factories;

use App\Models\User;
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

    // Các biến static này chỉ khởi tạo 1 lần duy nhất khi factory chạy, giúp tránh lặp lại việc lấy và loại các giá trị từ config
    protected static $restPositions;
    protected static $restDepartments;
    protected static $restRoles;

    public function definition(): array
    {
        self::$restPositions ??= array_values(Arr::except(config('positions'), ['admin', 'ceo','project_manager','leader']));
        self::$restDepartments ??= array_values(Arr::except(config('departments'), ['management']));

        $name = $this->faker->unique()->name;
        $email = str_replace('-', '', Str::slug($name)) . '@example.com';

        return [
            'name' => $name,
            'image' => null,
            'email' => $email,
            'role' => config('acl.roles.member'),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'position' => $this->faker->randomElement(self::$restPositions),
            'department' => $this->faker->randomElement(self::$restDepartments),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            if ($user->role) {
                $user->assignRole($user->role);
            }
        });
    }

    public function addSuperAdmin(): self
    {
        return $this->presetUser(
            'Super Admin',
            'superadmin@example.com',
            config('acl.roles.super_admin'),
            config('positions.admin'),
            config('departments.management'),
            'superadmin12345'
        );
    }

    public function addAdmin(string $name): self
    {
        $email = str_replace('-', '', Str::slug($name)) . uniqid() . '@example.com';
        return $this->presetUser(
            $name,
            $email,
            config('acl.roles.admin'),
            config('positions.admin'),
            config('departments.management'),
            'admin12345'
        );
    }

    public function addCEO(): self
    {
        return $this->presetUser(
            'CEO',
            'ceo@example.com',
            config('acl.roles.admin'),
            config('positions.ceo'),
            config('departments.management'),
            'ceo12345'
        );
    }

    public function addManager(string $department): self
    {
        return $this->presetDynamicUser(
            config('acl.roles.manager'),
            config('positions.project_manager'),
            $department,
            'manager12345'
        );
    }

    public function addLeader(string $department): self
    {
        return $this->presetDynamicUser(
            config('acl.roles.leader'),
            config('positions.leader'),
            $department,
            'leader12345'
        );
    }

    public function addMember(string $department): self
    {
        return $this->state(function (array $attributes) use($department) {
            $imageName = $this->createImage($attributes['name']);
            return [
                'image' => $imageName,
                'department'=>$department,
                'role' => config('acl.roles.member'),
                'password' => Hash::make('member12345'),
            ];
        });
    }

    public function addClient(): self
    {
        return $this->state(function (array $attributes) {
            $imageName = $this->createImage($attributes['name']);
            return [
                'image' => $imageName,
                'role' => config('acl.roles.client'),
                'position' => null,
                'department' => null,
                'password' => Hash::make('client12345'),
            ];
        });
    }

    protected function presetUser(string $name, string $email, string $role, ?string $position, ?string $department, string $password): self
    {
        $imageName = $this->createImage($name);
        return $this->state([
            'name' => $name,
            'image' => $imageName,
            'email' => $email,
            'role' => $role,
            'status' => 'active',
            'position' => $position,
            'department' => $department,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
        ]);
    }

    protected function presetDynamicUser(string $role, string $position, string $department, string $password): self
    {
        return $this->state(function (array $attributes) use ($role, $position, $department, $password) {
            return [
                'image' => $this->createImage($attributes['name']),
                'role' => $role,
                'position' => $position,
                'department' => $department,
                'status' => 'active',
                'password' => Hash::make($password),
            ];
        });
    }

    protected function createImage(string $name): string
    {
        $nameWithoutSpace = str_replace('-', '', Str::slug($name));
        $imageName = 'avatar_' . $nameWithoutSpace . '.svg';
        $avatarUrl = "https://api.dicebear.com/7.x/avataaars/svg?seed=" . urlencode($nameWithoutSpace);
        file_put_contents(public_path('images/users/' . $imageName), file_get_contents($avatarUrl));

        return $imageName;
    }
}
