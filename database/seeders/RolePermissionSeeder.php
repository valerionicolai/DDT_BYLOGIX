<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Admin panel access
            'access_admin_panel',
            
            // User management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Project management
            'view_projects',
            'create_projects',
            'edit_projects',
            'delete_projects',
            'manage_project_status',
            
            // Client management
            'view_clients',
            'create_clients',
            'edit_clients',
            'delete_clients',
            
            // Material types management
            'view_material_types',
            'create_material_types',
            'edit_material_types',
            'delete_material_types',
            
            // Document management
            'view_documents',
            'create_documents',
            'edit_documents',
            'delete_documents',
            'upload_documents',
            
            // Dashboard and analytics
            'view_dashboard',
            'view_analytics',
            'export_data',
            
            // System settings
            'manage_settings',
            'manage_roles',
            'manage_permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin role - has all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // Admin role - has most permissions except system management
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminPermissions = [
            'access_admin_panel',
            'view_users', 'create_users', 'edit_users',
            'view_projects', 'create_projects', 'edit_projects', 'delete_projects', 'manage_project_status',
            'view_clients', 'create_clients', 'edit_clients', 'delete_clients',
            'view_material_types', 'create_material_types', 'edit_material_types', 'delete_material_types',
            'view_documents', 'create_documents', 'edit_documents', 'delete_documents', 'upload_documents',
            'view_dashboard', 'view_analytics', 'export_data',
        ];
        $adminRole->syncPermissions($adminPermissions);

        // Manager role - can manage projects, clients, and materials
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerPermissions = [
            'access_admin_panel',
            'view_users',
            'view_projects', 'create_projects', 'edit_projects', 'manage_project_status',
            'view_clients', 'create_clients', 'edit_clients',
            'view_material_types', 'create_material_types', 'edit_material_types',
            'view_documents', 'create_documents', 'edit_documents', 'upload_documents',
            'view_dashboard', 'view_analytics',
        ];
        $managerRole->syncPermissions($managerPermissions);

        // User role - basic access
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userPermissions = [
            'view_projects',
            'view_clients',
            'view_material_types',
            'view_documents',
            'view_dashboard',
        ];
        $userRole->syncPermissions($userPermissions);

        // Assign roles to existing users based on their current role field
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role === 'admin') {
                $user->assignRole('admin');
            } else {
                $user->assignRole('user');
            }
        }

        // Create a super admin user if it doesn't exist
        $superAdmin = User::where('email', 'admin@dttbylogix.com')->first();
        if (!$superAdmin) {
            $superAdmin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@dttbylogix.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }
        $superAdmin->assignRole('super_admin');

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Super Admin created: admin@dttbylogix.com / password');
    }
}
