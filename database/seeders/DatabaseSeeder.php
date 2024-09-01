<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Project::factory()->count(10)->create();
        Task::factory()->count(10)->create();

        $permissions = [
            'project-list',
            'project-show',
            'project-create',
            'project-update',
            'project-delete',
            'task-list',
            'task-create',
            'task-update',
            'task-delete',
            'admin-list',
            'admin-create',
            'admin-update',
            'admin-delete',
            'role-list',
            'role-create',
            'role-update',
            'role-delete',
        ];

        foreach ($permissions as $permission) {
            $permission =  Permission::create(['name' => $permission]);
        }

        $user =  User::factory()->create([
            'name' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $role =  Role::create([
            'name' => 'superadmin',
        ]);


        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
