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

        User::create([
            'name'           => 'John Lotto',
            'email'          => 'john_lotto@gmail.com',
            'password'       => bcrypt('password'),
            'remember_token' => Str::random(60),
            'role_id'        => 2,
            'division_id'    => 2,
            'office_status'  => 'in_office'
        ]);
        User::create([
            'name'           => 'Yogha',
            'email'          => 'yogha@gmail.com',
            'password'       => bcrypt('password'),
            'remember_token' => Str::random(60),
            'role_id'        => 2,
            'division_id'    => 3,
            'office_status'  => null
        ]);
        User::create([
            'name'           => 'Lukman',
            'email'          => 'lukman@gmail.com',
            'password'       => bcrypt('password'),
            'remember_token' => Str::random(60),
            'role_id'        => 2,
            'division_id'    => 3,
            'office_status'  => null
        ]);
        User::create([
            'name'           => 'Kristina',
            'email'          => 'kristina@gmail.com',
            'password'       => bcrypt('password'),
            'remember_token' => Str::random(60),
            'role_id'        => 2,
            'division_id'    => 3,
            'office_status'  => null
        ]);
        User::create([
            'name'           => 'Jihad',
            'email'          => 'jihad@gmail.com',
            'password'       => bcrypt('password'),
            'remember_token' => Str::random(60),
            'role_id'        => 2,
            'division_id'    => 3,
            'office_status'  => null
        ]);
        User::create([
            'name'           => 'Devina',
            'email'          => 'secretary@gmail.com',
            'password'       => bcrypt('password'),
            'remember_token' => Str::random(60),
            'role_id'        => 3,
            'division_id'    => 1,
            'office_status'  => 'in_office'
        ]);
        User::create([
            'name'           => 'Office Boy',
            'email'          => 'ob@gmail.com',
            'password'       => bcrypt('password'),
            'remember_token' => Str::random(60),
            'role_id'        => 4,
            'office_status'  => null
        ]);
    }
}
