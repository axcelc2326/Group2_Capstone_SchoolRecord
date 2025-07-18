<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'create announcements',
            'view announcements',
            'register student',
            'approve student',
            'add grades',
            'view grades',
            'download sf5',
            'view analytics',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $parentRole = Role::firstOrCreate(['name' => 'parent']);

        // Assign permissions to roles
        $adminRole->syncPermissions([
            'create announcements',
            'view analytics',
        ]);

        $teacherRole->syncPermissions([
            'create announcements',
            'approve student',
            'add grades',
            'download sf5',
        ]);

        $parentRole->syncPermissions([
            'view announcements',
            'register student',
            'view grades',
        ]);

        // -------- Admin --------
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole($adminRole);

        // -------- Teachers --------
        foreach (range(1, 6) as $i) {
            $teacher = User::create([
                'name' => "Teacher_{$i} User",
                'email' => "teacher{$i}@gmail.com",
                'password' => Hash::make('password'),
            ]);
            $teacher->assignRole($teacherRole);
        }

        // -------- Dummy Parent --------
        $parent = User::create([
            'name' => 'Parent User',
            'email' => 'parent@example.com',
            'password' => Hash::make('password'),
        ]);
        $parent->assignRole($parentRole);
    }
}
