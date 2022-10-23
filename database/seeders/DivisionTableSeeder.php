<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Division::create([
            'name'           => 'CEO Office'
        ]);
        Division::create([
            'name'           => 'Finance'
        ]);
        Division::create([
            'name'           => 'Product'
        ]);;
    }
}
