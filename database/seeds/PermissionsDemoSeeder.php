<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsDemoSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'create']);
        Permission::create(['name' => 'update']);
        Permission::create(['name' => 'delete']);
        // // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'super-admin']);
        $role1->givePermissionTo('create');
        $role1->givePermissionTo('update');
        $role1->givePermissionTo('delete');

        $role2 = Role::create(['name' => 'admin']);
        $role2->givePermissionTo('create');
        $role2->givePermissionTo('update');
        
        $role3 = Role::create(['name' => 'customer']);
        $role4 = Role::create(['name' => 'directors']);
        $role5 = Role::create(['name' => 'contractor']);
        

        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = Factory(App\User::class)->create([
            'first_name' => 'Example Super-Admin User',
            'email' => 'superadmin@vinova.sg',
            'salutation' => 'test',
            'religion_id' => 1,
            'preferred_contact_by_id' => 1,
            'is_tgor' => 1,
            'status' => 54
            // 'service_id' => 1
            
        ]);
        $user->assignRole($role1);

        $user = Factory(App\User::class)->create([
            'first_name' => 'Example Admin User',
            'email' => 'admin@vinova.sg',
            'salutation' => 'test',
            'religion_id' => 1,
            'preferred_contact_by_id' => 1,
            'is_tgor' => 1,
            'status' => 54
            // 'service_id' => 1
        ]);
        $user->assignRole($role2);

        $user = Factory(App\User::class)->create([
            'first_name' => 'customer',
            'email' => 'customer@vinova.sg',
            'salutation' => 'test',
            'religion_id' => 1,
            'preferred_contact_by_id' => 1,
            'is_tgor' => 1,
            'status' => 54
            // 'service_id' => 1
            
        ]);
        $user->assignRole($role3);

    }
}