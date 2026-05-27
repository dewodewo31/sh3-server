    <?php

    namespace App\Console\Commands;

    use App\Models\Order;
    use App\Models\Attendance;
    use Illuminate\Console\Command;

    class GenerateAttendanceForExistingOrders extends Command
    {
        protected $signature = 'attendance:generate {--order-id= : Generate for specific order ID}';
        protected $description = 'Generate attendance records for existing orders';

        public function handle()
        {
            $query = Order::with(['event', 'participant'])
                ->whereIn('status', ['paid', 'free']);
            
            if ($orderId = $this->option('order-id')) {
                $query->where('id', $orderId);
            }
            
            $orders = $query->get();
            
            if ($orders->isEmpty()) {
                $this->error('No orders found.');
                return 1;
            }
            
            $this->info("Found {$orders->count()} orders to process...");
            
            $created = 0;
            $skipped = 0;
            
            foreach ($orders as $order) {
                // Check if attendance already exists
                $existingAttendance = Attendance::where('order_id', $order->id)->first();
                
                if ($existingAttendance) {
                    $this->warn("Order #{$order->id} already has attendance.");
                    $skipped++;
                    continue;
                }
                
                // Create attendance
                Attendance::create([
                    'order_id' => $order->id,
                    'event_id' => $order->event_id,
                    'participant_id' => $order->participant_id,
                    'status' => 'pending'
                ]);
                
                $this->info("✓ Created attendance for order #{$order->id} - {$order->participant->name}");
                $created++;
            }
            
            $this->newLine();
            $this->info("=== SUMMARY ===");
            $this->info("Created: {$created}");
            $this->info("Skipped: {$skipped}");
            $this->info("Total: " . ($created + $skipped));
            
            return 0;
        }
    }