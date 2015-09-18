<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
      //roles

      DB::table('roles')->insert([
        'name' => 'admin',
        'display_name' => 'administrador'
      ]);

      DB::table('roles')->insert([
        'name' => 'user',
        'display_name' => 'usuario'
      ]);

      //users
      DB::table('users')->insert([
        'name' => 'admin',
        'email' => 'admin@gmail.com',
        'password' => bcrypt('admin')
      ]);

      DB::table('users')->insert([
        'name' => 'user',
        'email' => 'user@gmail.com',
        'password' => bcrypt('user')
      ]);

      //users roles
      DB::table('role_user')->insert(['user_id' => 1, 'role_id' => 1]);
      DB::table('role_user')->insert(['user_id' => 1, 'role_id' => 2]);
      DB::table('role_user')->insert(['user_id' => 2, 'role_id' => 2]);
    }
}
