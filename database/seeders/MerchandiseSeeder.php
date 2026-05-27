<?php

namespace Database\Seeders;

use App\Models\Merchandise;
use Illuminate\Database\Seeder;

class MerchandiseSeeder extends Seeder
{
    public function run(): void
    {
        $merchandise = [
            [
                'name' => 'SH3 Official T-Shirt',
                'description' => 'Kaos resmi SH3 Event dengan bahan katun premium. Tersedia berbagai ukuran.',
                'price' => 150000,
                'stock' => 100,
                'category' => 'clothing',
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['Black', 'White'],
                'is_active' => true,
            ],
            [
                'name' => 'SH3 Baseball Cap',
                'description' => 'Topi baseball SH3 dengan desain embossed logo. Material premium, adjustable strap.',
                'price' => 85000,
                'stock' => 50,
                'category' => 'accessories',
                'sizes' => null,
                'colors' => ['Black', 'Navy', 'Gray'],
                'is_active' => true,
            ],
            [
                'name' => 'SH3 Tote Bag',
                'description' => 'Tote bag kanvas SH3 multifungsi. Cocok untuk belanja atau bawa perlengkapan event.',
                'price' => 65000,
                'stock' => 75,
                'category' => 'accessories',
                'sizes' => null,
                'colors' => ['Natural', 'Black'],
                'is_active' => true,
            ],
            [
                'name' => 'SH3 Pin Set',
                'description' => 'Set pin SH3 edisi terbatas. Terdiri dari 3 design berbeda. Limited edition!',
                'price' => 45000,
                'stock' => 30,
                'category' => 'collectibles',
                'sizes' => null,
                'colors' => null,
                'is_active' => true,
            ],
            [
                'name' => 'SH3 Hoodie',
                'description' => 'Hoodie SH3 dengan bahan fleece tebal dan hangat. Sangat cocok untuk acara outdoor.',
                'price' => 250000,
                'stock' => 40,
                'category' => 'clothing',
                'sizes' => ['M', 'L', 'XL'],
                'colors' => ['Black', 'Navy'],
                'is_active' => true,
            ],
        ];
        
        foreach ($merchandise as $item) {
            Merchandise::create($item);
        }
        
        $this->command->info('Merchandise seeded successfully!');
    }
}