<?php
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionsSeeder extends Seeder
{
    public function run()
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'view_dashboard',
            'create_user',
            'edit_user',
            'delete_user',
            'manage_sessions',
            'request_mentorship',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $mentor = Role::firstOrCreate(['name' => 'mentor']);
        $mentee = Role::firstOrCreate(['name' => 'mentee']);

        // Assign permissions to roles
        $superAdmin->givePermissionTo(Permission::all());

        $admin->givePermissionTo([
            'view_dashboard',
            'create_user',
            'edit_user',
        ]);

        $mentor->givePermissionTo([
            'view_dashboard',
            'manage_sessions',
        ]);

        $mentee->givePermissionTo([
            'view_dashboard',
            'request_mentorship',
        ]);
    }
}
