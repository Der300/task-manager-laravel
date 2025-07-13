<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolePermissions = config('acl.role_permissions');
        $permissions = config('acl.permissions');
        $roleNames = config('acl.roles');

        // tao permission
        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {             //view
                $permissionName = "$module.$action";    //user.view
                Permission::updateOrCreate(['name' => $permissionName]);
            }
        }

        foreach ($rolePermissions as $roleKey => $data) {
            $roleName = $roleNames[$roleKey] ?? null;
            if (!$roleName) continue;

            // tao roles
            $role = Role::updateOrCreate(
                ['name' => $roleName],
                ['level' => $data['level'] ?? null],
            );

            $assignPermissions = [];
            foreach ($data['permissions'] as $module =>  $actions) { //user=>['view', 'create']

                // tao permission
                if (is_array($actions)) {
                    foreach ($actions as $action) {             //view
                        $permissionName = "$module.$action";    //user.view
                        $assignPermissions[] = $permissionName;
                    }
                } elseif (is_string($actions) && $actions === 'all') {
                    $assignPermissions = Permission::all()->pluck('name')->toArray();
                    break;
                }
            }
            $role->syncPermissions(array_unique($assignPermissions));
        }
        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
