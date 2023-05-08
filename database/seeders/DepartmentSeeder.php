<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
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
                'name' => 'IT',
                'status' => 1,
                'created_at' => time()
            ], 
            [
                'name' => 'HR',
                'status' => 1,
                'created_at' => time()
            ],
            
        ];
        foreach ($input as $val) {
            Department::firstOrCreate($val);
        }
    }
}
