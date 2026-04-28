<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Order::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $participants = Participant::where('status', 'active')->get();
        $events = Event::all();
        
        if ($events->isEmpty()) {
            $this->command->error('No events found. Please run EventSeeder first.');
            return;
        }
        
        if ($participants->isEmpty()) {
            $this->command->error('No participants found. Please run ParticipantSeeder first.');
            return;
        }
        
        $this->command->info('Creating orders...');
        $ordersCreated = 0;
        
        // Create orders for each event
        foreach ($events as $event) {
            $maxOrders = min($event->quota, rand(5, min($event->quota, 30)));
            $participantsForEvent = $participants->random(min($maxOrders, $participants->count()));
            
            $this->command->info("Creating orders for event: {$event->title} ({$participantsForEvent->count()}/{$event->quota})");
            
            foreach ($participantsForEvent as $participant) {
                if ($event->price <= 0) {
                    $status = 'free';
                } else {
                    $rand = mt_rand(1, 100);
                    if ($rand <= 30) {
                        $status = 'pending';
                    } elseif ($rand <= 80) {
                        $status = 'paid';
                    } else {
                        $status = 'cancelled';
                    }
                }
                
                $createdAt = $this->randomDate('-30 days');
                
                Order::create([
                    'participant_id' => $participant->id,
                    'event_id' => $event->id,
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'ticket_code' => $this->generateTicketCode(),
                    'status' => $status,
                    'total_price' => $event->price,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                
                $ordersCreated++;
            }
        }
        
        // Create additional random orders
        $additionalOrders = rand(20, 50);
        $this->command->info("Creating {$additionalOrders} additional random orders...");
        
        for ($i = 0; $i < $additionalOrders; $i++) {
            $event = $events->random();
            $participant = $participants->random();
            
            $existingOrder = Order::where('participant_id', $participant->id)
                ->where('event_id', $event->id)
                ->exists();
            
            if (!$existingOrder) {
                if ($event->price <= 0) {
                    $status = 'free';
                } else {
                    $rand = mt_rand(1, 100);
                    if ($rand <= 25) {
                        $status = 'pending';
                    } elseif ($rand <= 75) {
                        $status = 'paid';
                    } else {
                        $status = 'cancelled';
                    }
                }
                
                $createdAt = $this->randomDate('-45 days');
                
                Order::create([
                    'participant_id' => $participant->id,
                    'event_id' => $event->id,
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'ticket_code' => $this->generateTicketCode(),
                    'status' => $status,
                    'total_price' => $event->price,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                
                $ordersCreated++;
            }
        }
        
        $this->command->info('');
        $this->command->info('=== ORDER SEEDER SUMMARY ===');
        $this->command->info("Total orders created: {$ordersCreated}");
        $this->command->info('Order Status Distribution:');
        $this->command->info('  - Pending: ' . Order::where('status', 'pending')->count());
        $this->command->info('  - Paid: ' . Order::where('status', 'paid')->count());
        $this->command->info('  - Free: ' . Order::where('status', 'free')->count());
        $this->command->info('  - Cancelled: ' . Order::where('status', 'cancelled')->count());
    }
    
    private function generateInvoiceNumber(): string
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
    }
    
    private function generateTicketCode(): string
    {
        return 'TCKT-' . strtoupper(Str::random(10));
    }
    
    private function randomDate(string $range, $from = null): string
    {
        if ($from) {
            $fromTimestamp = strtotime($from);
        } else {
            $fromTimestamp = strtotime('-60 days');
        }
        
        $randomTimestamp = mt_rand($fromTimestamp, time());
        return date('Y-m-d H:i:s', $randomTimestamp);
    }
}