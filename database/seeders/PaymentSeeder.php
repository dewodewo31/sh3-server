<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Payment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $paidOrders = Order::where('status', 'paid')->get();
        $pendingOrders = Order::where('status', 'pending')->get();
        
        $paymentMethods = [
            'Bank Transfer BCA',
            'Bank Transfer Mandiri', 
            'Bank Transfer BRI',
            'QRIS',
            'DANA',
            'OVO',
            'GoPay'
        ];
        
        $paymentsCreated = 0;
        
        $this->command->info('Creating payments for paid orders...');
        
        foreach ($paidOrders as $order) {
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $paidAt = $this->randomDate('+2 days', $order->created_at);
            
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'payment_proof' => $this->generateRandomProofImage(),
                'amount' => $order->total_price,
                'paid_at' => $paidAt,
                'status' => 'confirmed',
                // 'notes' => $this->getRandomVerificationNote(), // Hapus notes
                'created_at' => $paidAt,
                'updated_at' => $paidAt,
            ]);
            
            $paymentsCreated++;
        }
        
        $this->command->info('Creating payments for pending orders...');
        
        $pendingWithPayment = $pendingOrders->take(ceil($pendingOrders->count() / 2));
        
        foreach ($pendingWithPayment as $order) {
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $paidAt = $this->randomDate('+1 day', $order->created_at);
            
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'payment_proof' => $this->generateRandomProofImage(),
                'amount' => $order->total_price,
                'paid_at' => $paidAt,
                'status' => 'pending',
                // 'notes' => 'Menunggu verifikasi admin', // Hapus notes
                'created_at' => $paidAt,
                'updated_at' => $paidAt,
            ]);
            
            $paymentsCreated++;
        }
        
        $this->command->info('Creating rejected payments...');
        
        $cancelledOrders = Order::where('status', 'cancelled')
            ->where('total_price', '>', 0)
            ->take(rand(5, 15))
            ->get();
        
        foreach ($cancelledOrders as $order) {
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $paidAt = $this->randomDate('+1 day', $order->created_at);
            
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'payment_proof' => $this->generateRandomProofImage(),
                'amount' => $order->total_price,
                'paid_at' => $paidAt,
                'status' => 'rejected',
                // 'notes' => $this->getRandomRejectionNote(), // Hapus notes
                'created_at' => $paidAt,
                'updated_at' => $paidAt,
            ]);
            
            $paymentsCreated++;
        }
        
        $this->command->info('');
        $this->command->info('=== PAYMENT SEEDER SUMMARY ===');
        $this->command->info("Total payments created: {$paymentsCreated}");
        $this->command->info('Payment Status Distribution:');
        $this->command->info('  - Pending: ' . Payment::where('status', 'pending')->count());
        $this->command->info('  - Confirmed: ' . Payment::where('status', 'confirmed')->count());
        $this->command->info('  - Rejected: ' . Payment::where('status', 'rejected')->count());
        
        $totalAmount = Payment::where('status', 'confirmed')->sum('amount');
        $this->command->info("Total confirmed amount: Rp " . number_format($totalAmount, 0, ',', '.'));
    }
    
    private function generateRandomProofImage(): string
    {
        $images = [
            'payment-proofs/sample-bca.jpg',
            'payment-proofs/sample-mandiri.jpg',
            'payment-proofs/sample-bri.jpg',
            'payment-proofs/sample-qris.jpg',
            'payment-proofs/sample-gopay.jpg',
            'payment-proofs/sample-ovo.jpg',
            'payment-proofs/sample-dana.jpg',
        ];
        
        return $images[array_rand($images)];
    }
    
    private function randomDate(string $range, $from = null): string
    {
        if ($from) {
            $fromTimestamp = strtotime($from);
        } else {
            $fromTimestamp = strtotime('-30 days');
        }
        
        if ($range == '+1 day') {
            $toTimestamp = strtotime('+1 day', $fromTimestamp);
        } elseif ($range == '+2 days') {
            $toTimestamp = strtotime('+2 days', $fromTimestamp);
        } else {
            $toTimestamp = time();
        }
        
        $randomTimestamp = mt_rand($fromTimestamp, $toTimestamp);
        return date('Y-m-d H:i:s', $randomTimestamp);
    }
}