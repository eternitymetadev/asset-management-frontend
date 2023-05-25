<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleTableSeeder extends Seeder
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
                'name' => 'Admin',
                'slug' => 'admin',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'IT',
                'slug' => 'it',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'HR',
                'slug' => 'hr',
                'status' => 1,
                'created_at' => time()
            ],
            
            
        ];
        foreach ($input as $val) {
            Role::firstOrCreate($val);
        }
    }
}
