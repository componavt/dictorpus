<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = [
            'email'    => 'admin@admin.com',
            'password' => 'adminadmin',
        ];
        $adminUser = Sentinel::registerAndActivate($admin);
        $role = [
            'name' => 'Администратор',
            'slug' => 'admin',
            'permissions' => [
                'admin' => true,
            ]
        ];
        $adminRole = Sentinel::getRoleRepository()->createModel()->fill($role)->save();
        $adminUser->roles()->attach($adminRole);
        $role = [
            'name' => 'Пользователь',
            'slug' => 'user',
        ];
        $userRole = Sentinel::getRoleRepository()->createModel()->fill($role)->save();
        $role = [
            'name' => 'Забанен',
            'slug' => 'banned',
        ];
        $banRole = Sentinel::getRoleRepository()->createModel()->fill($role)->save();
    }
}
