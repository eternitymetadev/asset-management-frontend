<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
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
                'name' => 'Laptop',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'Furniture',
                'status' => 1,
                'created_at' => time()
            ],
            
            

        ];
        foreach ($input as $val) {
            Category::firstOrCreate($val);
        }
    }
}
