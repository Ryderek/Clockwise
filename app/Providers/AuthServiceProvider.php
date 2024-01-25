<?php

namespace App\Providers;

use DB;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define "view_orders" permission
        Gate::define('view_orders', function ($user) {
            $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $user->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
            foreach($perms as $perm){
                if ($perm->name == 'view_orders'){
                    return true;
                }
            }
            return false;
        });

        // Define "edit_orders" permission
        Gate::define('edit_orders', function ($user) {
            $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $user->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
            foreach($perms as $perm){
                if ($perm->name == 'edit_orders'){
                    return true;
                }
            }
            return false;
        });

        // Define "deploy_orders" permission
        Gate::define('deploy_orders', function ($user) {
            $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $user->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
            foreach($perms as $perm){
                if ($perm->name == 'deploy_orders'){
                    return true;
                }
            }
            return false;
        });

        // Define "create_orders" permission
        Gate::define('create_orders', function ($user) {
            $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $user->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
            foreach($perms as $perm){
                if ($perm->name == 'create_orders'){
                    return true;
                }
            }
            return false;
        });

        // Define "manage_employees" permission
        Gate::define('manage_employees', function ($user) {
            $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $user->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
            foreach($perms as $perm){
                if ($perm->name == 'manage_employees'){
                    return true;
                }
            }
            return false;
        });

        // Define "manage_employees_authcards" permission
        Gate::define('manage_employees_authcards', function ($user) {
            $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $user->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
            foreach($perms as $perm){
                if ($perm->name == 'manage_employees_authcards'){
                    return true;
                }
            }
            return false;
        });

        // Define "manage_groups" permission
        Gate::define('manage_groups', function ($user) {
            $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $user->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
            foreach($perms as $perm){
                if ($perm->name == 'manage_groups'){
                    return true;
                }
            }
            return false;
        });

        // Define "manage_roles" permission
        Gate::define('manage_roles', function ($user) {
            $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $user->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
            foreach($perms as $perm){
                if ($perm->name == 'manage_roles'){
                    return true;
                }
            }
            return false;
        });

        // Define "manage_tools" permission
        Gate::define('manage_tools', function ($user) {
            $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $user->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
            foreach($perms as $perm){
                if ($perm->name == 'manage_tools'){
                    return true;
                }
            }
            return false;
        });

        // Define "manage_accounting" permission
        Gate::define('manage_accounting', function ($user) {
            $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $user->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
            foreach($perms as $perm){
                if ($perm->name == 'manage_accounting'){
                    return true;
                }
            }
            return false;
        });

        // Define "manage_salaries" permission
        Gate::define('manage_salaries', function ($user) {
            $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $user->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
            foreach($perms as $perm){
                if ($perm->name == 'manage_salaries'){
                    return true;
                }
            }
            return false;
        });

        // Define "manage_settlement" permission
        Gate::define('manage_settlement', function ($user) {
            $perms = DB::table('admin_group_permissions_associations')->select("*")->where('groupId', $user->groupId)->join('admin_group_permissions', 'admin_group_permissions.id', '=', 'admin_group_permissions_associations.permissionId')->get();
            foreach($perms as $perm){
                if ($perm->name == 'manage_settlement'){
                    return true;
                }
            }
            return false;
        });
    }
}
