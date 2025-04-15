<?php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

public function run()
{
    // Super Admin
    $superAdmin = Role::findByName('super-admin');
    $superAdmin->givePermissionTo(Permission::all());

    // Department Admin
    $departmentAdmin = Role::findByName('department-admin');
    $departmentAdmin->givePermissionTo([
        'create-user',
        'edit-user',
        'view-dashboard',
        'assign-department',
    ]);

    // Mentor
    $mentor = Role::findByName('mentor');
    $mentor->givePermissionTo([
        'join-session',
        'view-dashboard',
    ]);

    // Mentee
    $mentee = Role::findByName('mentee');
    $mentee->givePermissionTo([
        'join-session',
    ]);
}
