<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
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
                'name' => 'Dell',
                'status' => 1,
                'created_at' => time()
            ],
            [
                'name' => 'Lenovo',
                'status' => 1,
                'created_at' => time()
            ],
            

        ];
        foreach ($input as $val) {
            Brand::firstOrCreate($val);
        }
    }
}
