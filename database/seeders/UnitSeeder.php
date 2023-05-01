<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input = [
            [
                'name' => 'SD1',
                'description' => '',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'SD2',
                'description' => '',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'SD3',
                'description' => '',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'SD4',
                'description' => '',
                'status' => 1,
                'created_at' => time()
            ],
            

        ];
        foreach ($input as $val) {
            Unit::firstOrCreate($val);
        }
    }
}
