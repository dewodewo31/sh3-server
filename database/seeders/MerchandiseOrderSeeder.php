<?php

namespace Database\Seeders;

use App\Models\Merchandise;
use App\Models\MerchandiseOrder;
use App\Models\Participant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MerchandiseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        MerchandiseOrder::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $merchandise = Merchandise::all();
        $participants = Participant::where('status', 'active')->get();
        
        if ($merchandise->isEmpty()) {
            $this->command->error('No merchandise found. Please run MerchandiseSeeder first.');
            return;
        }
        
        if ($participants->isEmpty()) {
            $this->command->error('No participants found. Please run ParticipantSeeder first.');
            return;
        }
        
        $this->command->info('Creating merchandise orders...');
        
        $statuses = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $colors = ['Black', 'White', 'Navy', 'Gray', 'Red', 'Blue'];
        $shippingCouriers = ['JNE', 'J&T', 'SiCepat', 'Pos Indonesia', 'Grab Express', 'GoSend'];
        
        $ordersCreated = 0;
        
        // Create orders for each merchandise
        foreach ($merchandise as $item) {
            // Random number of orders per item (5-20 orders)
            $numOrders = rand(5, 20);
            $participantsForItem = $participants->random(min($numOrders, $participants->count()));
            
            $this->command->info("Creating orders for: {$item->name} ({$participantsForItem->count()} orders)");
            
            foreach ($participantsForItem as $participant) {
                $quantity = rand(1, 3);
                $totalPrice = $item->price * $quantity;
                $status = $this->getWeightedStatus();
                $createdAt = $this->randomDate('-60 days');
                
                $orderData = [
                    'participant_id' => $participant->id,
                    'merchandise_id' => $item->id,
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'quantity' => $quantity,
                    'size' => $item->sizes ? $item->sizes[array_rand($item->sizes)] : null,
                    'color' => $item->colors ? $item->colors[array_rand($item->colors)] : null,
                    'unit_price' => $item->price,
                    'total_price' => $totalPrice,
                    'status' => $status,
                    'shipping_address' => $this->generateRandomAddress(),
                    'shipping_phone' => '0812' . rand(10000000, 99999999),
                    'shipping_courier' => $status != 'pending' ? $shippingCouriers[array_rand($shippingCouriers)] : null,
                    'tracking_number' => $status != 'pending' ? $this->generateTrackingNumber() : null,
                    'notes' => rand(1, 100) <= 30 ? $this->getRandomNote() : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
                
                // Add paid_at for paid status
                if (in_array($status, ['paid', 'processing', 'shipped', 'delivered'])) {
                    $orderData['paid_at'] = $this->randomDate('+2 days', $createdAt);
                    $orderData['updated_at'] = $orderData['paid_at'];
                }
                
                // Add shipped_at for shipped status
                if (in_array($status, ['shipped', 'delivered'])) {
                    $orderData['shipped_at'] = $this->randomDate('+3 days', $orderData['paid_at'] ?? $createdAt);
                    $orderData['updated_at'] = $orderData['shipped_at'];
                }
                
                // Add delivered_at for delivered status
                if ($status == 'delivered') {
                    $orderData['delivered_at'] = $this->randomDate('+5 days', $orderData['shipped_at'] ?? $createdAt);
                    $orderData['updated_at'] = $orderData['delivered_at'];
                }
                
                MerchandiseOrder::create($orderData);
                $ordersCreated++;
            }
        }
        
        // Create additional random orders
        $additionalOrders = rand(20, 40);
        $this->command->info("Creating {$additionalOrders} additional random orders...");
        
        for ($i = 0; $i < $additionalOrders; $i++) {
            $item = $merchandise->random();
            $participant = $participants->random();
            $quantity = rand(1, 5);
            $totalPrice = $item->price * $quantity;
            $status = $this->getWeightedStatus();
            $createdAt = $this->randomDate('-90 days');
            
            $orderData = [
                'participant_id' => $participant->id,
                'merchandise_id' => $item->id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'quantity' => $quantity,
                'size' => $item->sizes ? $item->sizes[array_rand($item->sizes)] : null,
                'color' => $item->colors ? $item->colors[array_rand($item->colors)] : null,
                'unit_price' => $item->price,
                'total_price' => $totalPrice,
                'status' => $status,
                'shipping_address' => $this->generateRandomAddress(),
                'shipping_phone' => '0812' . rand(10000000, 99999999),
                'shipping_courier' => $status != 'pending' ? $shippingCouriers[array_rand($shippingCouriers)] : null,
                'tracking_number' => $status != 'pending' ? $this->generateTrackingNumber() : null,
                'notes' => rand(1, 100) <= 20 ? $this->getRandomNote() : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            
            if (in_array($status, ['paid', 'processing', 'shipped', 'delivered'])) {
                $orderData['paid_at'] = $this->randomDate('+2 days', $createdAt);
                $orderData['updated_at'] = $orderData['paid_at'];
            }
            
            if (in_array($status, ['shipped', 'delivered'])) {
                $orderData['shipped_at'] = $this->randomDate('+3 days', $orderData['paid_at'] ?? $createdAt);
                $orderData['updated_at'] = $orderData['shipped_at'];
            }
            
            if ($status == 'delivered') {
                $orderData['delivered_at'] = $this->randomDate('+5 days', $orderData['shipped_at'] ?? $createdAt);
                $orderData['updated_at'] = $orderData['delivered_at'];
            }
            
            MerchandiseOrder::create($orderData);
            $ordersCreated++;
        }
        
        // Update stock based on delivered orders
        $this->updateStockBasedOnOrders();
        
        $this->command->info('');
        $this->command->info('=== MERCHANDISE ORDER SEEDER SUMMARY ===');
        $this->command->info("Total orders created: {$ordersCreated}");
        $this->command->info('');
        $this->command->info('Order Status Distribution:');
        $this->command->info('  - Pending: ' . MerchandiseOrder::where('status', 'pending')->count());
        $this->command->info('  - Paid: ' . MerchandiseOrder::where('status', 'paid')->count());
        $this->command->info('  - Processing: ' . MerchandiseOrder::where('status', 'processing')->count());
        $this->command->info('  - Shipped: ' . MerchandiseOrder::where('status', 'shipped')->count());
        $this->command->info('  - Delivered: ' . MerchandiseOrder::where('status', 'delivered')->count());
        $this->command->info('  - Cancelled: ' . MerchandiseOrder::where('status', 'cancelled')->count());
        $this->command->info('');
        
        $totalRevenue = MerchandiseOrder::where('status', 'delivered')->sum('total_price');
        $this->command->info("Total revenue from delivered orders: Rp " . number_format($totalRevenue, 0, ',', '.'));
    }
    
    /**
     * Generate random invoice number
     */
    private function generateInvoiceNumber(): string
    {
        return 'INV-MERCH-' . date('Ymd') . '-' . strtoupper(Str::random(6));
    }
    
    /**
     * Generate random tracking number
     */
    private function generateTrackingNumber(): string
    {
        return strtoupper(Str::random(4)) . rand(100000000, 999999999);
    }
    
    /**
     * Generate random address
     */
    private function generateRandomAddress(): string
    {
        $cities = ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Yogyakarta', 'Denpasar', 'Makassar', 'Palembang', 'Malang'];
        $streets = ['Jl. Sudirman', 'Jl. Thamrin', 'Jl. Gatot Subroto', 'Jl. Diponegoro', 'Jl. Pahlawan', 'Jl. Merdeka'];
        
        $city = $cities[array_rand($cities)];
        $street = $streets[array_rand($streets)];
        $number = rand(1, 200);
        
        return "{$street} No. {$number}, {$city}";
    }
    
    /**
     * Get weighted status distribution
     */
    private function getWeightedStatus(): string
    {
        $rand = mt_rand(1, 100);
        
        // Distribution:
        // 15% pending
        // 20% paid
        // 15% processing
        // 20% shipped
        // 20% delivered
        // 10% cancelled
        
        if ($rand <= 15) {
            return 'pending';
        } elseif ($rand <= 35) {
            return 'paid';
        } elseif ($rand <= 50) {
            return 'processing';
        } elseif ($rand <= 70) {
            return 'shipped';
        } elseif ($rand <= 90) {
            return 'delivered';
        } else {
            return 'cancelled';
        }
    }
    
    /**
     * Random date generator
     */
    private function randomDate(string $range, $from = null): string
    {
        if ($from) {
            $fromTimestamp = strtotime($from);
        } else {
            $fromTimestamp = strtotime('-90 days');
        }
        
        if ($range == '-60 days') {
            $toTimestamp = time();
        } elseif ($range == '-90 days') {
            $toTimestamp = time();
        } elseif ($range == '+2 days') {
            $toTimestamp = strtotime('+2 days', $fromTimestamp);
        } elseif ($range == '+3 days') {
            $toTimestamp = strtotime('+3 days', $fromTimestamp);
        } elseif ($range == '+5 days') {
            $toTimestamp = strtotime('+5 days', $fromTimestamp);
        } else {
            $toTimestamp = time();
        }
        
        $randomTimestamp = mt_rand($fromTimestamp, $toTimestamp);
        return date('Y-m-d H:i:s', $randomTimestamp);
    }
    
    /**
     * Get random note
     */
    private function getRandomNote(): string
    {
        $notes = [
            'Tolong pakai packing yang aman',
            'Kirim via JNE YES',
            'Request: tambah stiker SH3',
            'Kado untuk teman, tolong dibungkus rapih',
            'Ukuran M tapi agak longgar ya',
            'Warna hitam tolong yang matte',
            'Kirim cepat karena untuk acara weekend ini',
            'Tolong kasih packing bubble wrap',
        ];
        
        return $notes[array_rand($notes)];
    }
    
    /**
     * Update stock based on delivered orders
     */
    private function updateStockBasedOnOrders(): void
    {
        $deliveredOrders = MerchandiseOrder::where('status', 'delivered')->get();
        
        foreach ($deliveredOrders as $order) {
            $merchandise = $order->merchandise;
            // Stock sudah berkurang saat order dibuat, tapi kita update sold_count
            $merchandise->sold_count = $merchandise->orders()
                ->where('status', 'delivered')
                ->sum('quantity');
            $merchandise->save();
        }
        
        $this->command->info('Stock updated based on delivered orders');
    }
}