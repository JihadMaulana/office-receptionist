<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::count() == 0) {
            User::create([
                'name'           => 'Office Admin',
                'email'          => 'office.admin@gmail.com',
                'password'       => bcrypt('password'),
                'remember_token' => Str::random(60),
                'role_id'        => 1,
                'office_status'  => null
            ]);
        }
    }
}
