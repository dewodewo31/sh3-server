<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $categories = [
            ['name' => 'Technology'],
            ['name' => 'Business'],
            ['name' => 'Education'],
            ['name' => 'Health'],
            ['name' => 'Entertainment'],
            ['name' => 'Art & Culture'],
            ['name' => 'Sports'],
            ['name' => 'Music'],
        ];
        
        foreach ($categories as $category) {
            Category::create($category);
        }
        
        $this->command->info('Categories seeded successfully!');
    }
}