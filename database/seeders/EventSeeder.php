<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Bersihkan data lama
        Event::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Get categories and organizers
        $categories = Category::all();
        $organizers = User::where('role', 'organizer')->get();
        
        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please run CategorySeeder first.');
            return;
        }
        
        if ($organizers->isEmpty()) {
            $this->command->error('No organizers found. Please run UserSeeder first.');
            return;
        }
        
        $events = [
            // ==================== TECHNOLOGY EVENTS ====================
            [
                'title' => 'Laracon Indonesia 2024',
                'description' => 'Konferensi Laravel tahunan terbesar di Indonesia. Akan ada banyak session menarik dari speaker lokal dan internasional. Pelajari best practices, teknik terbaru, dan networking dengan developer lainnya.',
                'location' => 'Jakarta Convention Center, Jakarta',
                'key_point' => ['Meet Laravel Core Team', 'Workshop Laravel 11', 'Networking Session', 'Exclusive Merchandise', 'Sertifikat Peserta'],
                'start_date' => '2024-12-15 09:00:00',
                'end_date' => '2024-12-17 18:00:00',
                'price' => 1500000,
                'quota' => 500,
                'category' => 'Technology'
            ],
            [
                'title' => 'React Summit 2024',
                'description' => 'Summit terbesar untuk developer React. Pelajari teknologi terbaru seperti React 19, Server Components, dan masih banyak lagi. Cocok untuk developer junior hingga senior.',
                'location' => 'Grand City Convention Hall, Surabaya',
                'key_point' => ['React 19 Workshop', 'State Management Deep Dive', 'Performance Optimization', 'Panel Discussion with Experts'],
                'start_date' => '2024-11-20 10:00:00',
                'end_date' => '2024-11-22 17:00:00',
                'price' => 1200000,
                'quota' => 300,
                'category' => 'Technology'
            ],
            [
                'title' => 'Cloud Computing Bootcamp',
                'description' => 'Bootcamp intensif 3 hari tentang cloud computing. Materi mencakup AWS, Google Cloud, dan Azure. Siapkan dirimu untuk menjadi cloud engineer profesional.',
                'location' => 'Digital Hub, Bandung',
                'key_point' => ['AWS Certified Preparation', 'Hands-on Lab', 'Real Project', 'Career Consultation', 'Free Cloud Credits'],
                'start_date' => '2024-10-10 08:00:00',
                'end_date' => '2024-10-12 20:00:00',
                'price' => 2500000,
                'quota' => 100,
                'category' => 'Technology'
            ],
            
            // ==================== BUSINESS & ENTREPRENEURSHIP ====================
            [
                'title' => 'Startup Founder Summit',
                'description' => 'Event khusus untuk para founder startup. Belajar dari pengalaman founder sukses, pitching session, dan opportunity untuk mendapatkan pendanaan.',
                'location' => 'ICE BSD, Tangerang',
                'key_point' => ['Pitching Session', 'Investor Meeting', 'Legal Workshop', 'Marketing Strategy', 'Networking Dinner'],
                'start_date' => '2024-11-05 09:00:00',
                'end_date' => '2024-11-06 18:00:00',
                'price' => 3500000,
                'quota' => 200,
                'category' => 'Business'
            ],
            [
                'title' => 'Digital Marketing Masterclass',
                'description' => 'Pelajari strategi digital marketing terkini dari praktisi berpengalaman. Materi SEO, Social Media Ads, Content Marketing, dan Email Marketing.',
                'location' => 'Harris Hotel, Yogyakarta',
                'key_point' => ['SEO Optimization', 'Google Ads Strategy', 'Social Media Growth', 'Content Calendar Planning', 'Case Study Analysis'],
                'start_date' => '2024-12-01 10:00:00',
                'end_date' => '2024-12-02 17:00:00',
                'price' => 800000,
                'quota' => 150,
                'category' => 'Business'
            ],
            
            // ==================== EDUCATION & WORKSHOP ====================
            [
                'title' => 'Data Science Workshop',
                'description' => 'Workshop intensif data science untuk pemula. Pelajari Python, Pandas, Matplotlib, dan Machine Learning dasar. Tidak diperlukan pengalaman coding sebelumnya.',
                'location' => 'Co-working Space, Malang',
                'key_point' => ['Python Basics', 'Data Visualization', 'Machine Learning Intro', 'Final Project', 'Mentorship Session'],
                'start_date' => '2024-11-25 09:00:00',
                'end_date' => '2024-11-27 16:00:00',
                'price' => 500000,
                'quota' => 50,
                'category' => 'Education'
            ],
            [
                'title' => 'UI/UX Design Intensive',
                'description' => 'Pelajari prinsip-prinsip UI/UX design dari dasar hingga mahir. Gunakan Figma untuk membuat prototype aplikasi yang menarik dan user-friendly.',
                'location' => 'Design Center, Semarang',
                'key_point' => ['Figma Mastery', 'User Research', 'Wireframing', 'Prototyping', 'Portfolio Building'],
                'start_date' => '2025-01-15 10:00:00',
                'end_date' => '2025-01-17 18:00:00',
                'price' => 750000,
                'quota' => 40,
                'category' => 'Education'
            ],
            
            // ==================== HEALTH & WELLNESS ====================
            [
                'title' => 'Mental Health Awareness Seminar',
                'description' => 'Seminar tentang pentingnya kesehatan mental di era digital. Akan ada sesi konsultasi gratis dengan psikolog profesional.',
                'location' => 'Santika Hotel, Medan',
                'key_point' => ['Stress Management', 'Work-Life Balance', 'Mindfulness Practice', 'Free Consultation', 'Mental Health Screening'],
                'start_date' => '2024-10-20 08:00:00',
                'end_date' => '2024-10-20 17:00:00',
                'price' => 100000,
                'quota' => 200,
                'category' => 'Health'
            ],
            [
                'title' => 'Yoga & Meditation Retreat',
                'description' => 'Retreat 3 hari untuk relaksasi dan meditasi. Cocok untuk anda yang ingin melepas penat dan menemukan ketenangan batin.',
                'location' => 'Puncak Resort, Bogor',
                'key_point' => ['Morning Yoga', 'Guided Meditation', 'Nature Walk', 'Healthy Meals', 'Wellness Workshop'],
                'start_date' => '2024-12-10 07:00:00',
                'end_date' => '2024-12-12 20:00:00',
                'price' => 1500000,
                'quota' => 60,
                'category' => 'Health'
            ],
            
            // ==================== FREE EVENTS ====================
            [
                'title' => 'Community Gathering: Tech Talk',
                'description' => 'Acara gratis untuk komunitas teknologi. Diskusi ringan, sharing pengalaman, dan networking. Bawa laptopmu untuk coding bersama!',
                'location' => 'Tech Hub, Denpasar',
                'key_point' => ['Free Coffee', 'Lightning Talks', 'Coding Session', 'Pizza & Beer', 'Prizes & Giveaways'],
                'start_date' => '2024-11-30 14:00:00',
                'end_date' => '2024-11-30 21:00:00',
                'price' => 0,
                'quota' => 100,
                'category' => 'Technology'
            ],
            [
                'title' => 'Career Expo 2024',
                'description' => 'Pameran karir gratis dengan banyak perusahaan ternama. Kesempatan untuk mendapatkan pekerjaan impianmu. Siapkan CV dan portfolio terbaikmu!',
                'location' => 'Convention Hall, Palembang',
                'key_point' => ['Job Interview Session', 'CV Review', 'Career Consultation', 'Company Booths', 'Free SWAG'],
                'start_date' => '2025-01-20 09:00:00',
                'end_date' => '2025-01-20 17:00:00',
                'price' => 0,
                'quota' => 1000,
                'category' => 'Business'
            ],
            
            // ==================== PAST EVENTS (Finished) ====================
            [
                'title' => 'PHP Indonesia Conference 2023',
                'description' => 'Konferensi PHP Indonesia tahun 2023. Sudah selesai, tapi materinya masih bisa diakses secara online.',
                'location' => 'Online Event',
                'key_point' => ['PHP 8.3 Updates', 'Framework Comparison', 'Security Best Practices', 'Database Optimization'],
                'start_date' => '2023-11-15 09:00:00',
                'end_date' => '2023-11-17 18:00:00',
                'price' => 500000,
                'quota' => 300,
                'category' => 'Technology'
            ],
            [
                'title' => 'Digital Transformation Summit 2023',
                'description' => 'Summit tentang transformasi digital untuk perusahaan. Event sudah selesai, rekaman tersedia for premium member.',
                'location' => 'Grand Hotel, Surabaya',
                'key_point' => ['AI Implementation', 'Digital Strategy', 'Change Management', 'Case Studies'],
                'start_date' => '2023-10-05 08:00:00',
                'end_date' => '2023-10-06 17:00:00',
                'price' => 2000000,
                'quota' => 150,
                'category' => 'Business'
            ],
            
            // ==================== ONGOING EVENTS ====================
            [
                'title' => 'Creative Writing Bootcamp',
                'description' => 'Bootcamp menulis kreatif yang sedang berlangsung. Masih bisa join untuk sesi yang tersisa!',
                'location' => 'Literary Hub, Bandung',
                'key_point' => ['Character Development', 'Plot Structure', 'Editing Workshop', 'Publishing Tips'],
                'start_date' => '2024-04-20 10:00:00',
                'end_date' => '2024-05-10 16:00:00',
                'price' => 600000,
                'quota' => 30,
                'category' => 'Education'
            ],
            
            // ==================== UPCOMING HOT EVENTS ====================
            [
                'title' => 'Artificial Intelligence Symposium',
                'description' => 'Symposium tentang AI dan masa depan teknologi. Akan ada demo live dari latest AI models.',
                'location' => 'AI Center, Jakarta',
                'key_point' => ['GPT-4 Applications', 'Computer Vision', 'AI Ethics', 'Future Predictions', 'Live Demos'],
                'start_date' => '2025-03-10 09:00:00',
                'end_date' => '2025-03-12 18:00:00',
                'price' => 1800000,
                'quota' => 250,
                'category' => 'Technology'
            ],
            [
                'title' => 'Women in Tech Conference',
                'description' => 'Konferensi untuk mendukung perempuan di industri teknologi. Ajak kolega perempuanmu untuk bergabung!',
                'location' => 'Pullman Hotel, Surabaya',
                'key_point' => ['Empowerment Session', 'Leadership Workshop', 'Mentorship Program', 'Scholarship Opportunities'],
                'start_date' => '2025-03-08 09:00:00',
                'end_date' => '2025-03-08 18:00:00',
                'price' => 450000,
                'quota' => 300,
                'category' => 'Technology'
            ],
        ];
        
        $this->command->info('Creating events...');
        $eventsCreated = 0;
        
        foreach ($events as $eventData) {
            // Find category by name
            $category = $categories->where('name', $eventData['category'])->first();
            
            if (!$category) {
                $this->command->warn("Category '{$eventData['category']}' not found, using first category.");
                $category = $categories->first();
            }
            
            // Random organizer
            $organizer = $organizers->random();
            
            // Generate random image (optional)
            $imagePath = $this->getRandomImage($eventData['title']);
            
            Event::create([
                'title' => $eventData['title'],
                'description' => $eventData['description'],
                'location' => $eventData['location'],
                'key_point' => $eventData['key_point'],
                'image' => $imagePath,
                'start_date' => $eventData['start_date'],
                'end_date' => $eventData['end_date'],
                'category_id' => $category->id,
                'price' => $eventData['price'],
                'quota' => $eventData['quota'],
                'created_by' => $organizer->id,
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now(),
            ]);
            
            $eventsCreated++;
            
            if ($eventsCreated % 3 == 0) {
                $this->command->info("Created {$eventsCreated} events...");
            }
        }
        
        $this->command->info('');
        $this->command->info('=== EVENT SEEDER SUMMARY ===');
        $this->command->info("Total events created: {$eventsCreated}");
        $this->command->info('');
        $this->command->info('Event Status Distribution:');
        
        $upcomingCount = Event::where('start_date', '>', now())->count();
        $ongoingCount = Event::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
        $finishedCount = Event::where('end_date', '<', now())->count();
        
        $this->command->info('  - Upcoming: ' . $upcomingCount);
        $this->command->info('  - Ongoing: ' . $ongoingCount);
        $this->command->info('  - Finished: ' . $finishedCount);
        
        // Event revenue potential
        $totalPotentialRevenue = Event::sum('price') * 0.7; // Asumsi 70% quota terisi
        $this->command->info('');
        $this->command->info("Potential revenue: Rp " . number_format($totalPotentialRevenue, 0, ',', '.'));
    }
    
    /**
     * Get random image path based on event title
     */
    private function getRandomImage(string $title): ?string
    {
        // Sample images - you can add more or keep null for no image
        $images = [
            'events/tech-conference.jpg',
            'events/business-seminar.jpg',
            'events/workshop.jpg',
            'events/health-retreat.jpg',
            'events/education-bootcamp.jpg',
        ];
        
        // 70% chance to have an image
        if (rand(1, 100) <= 70) {
            return $images[array_rand($images)];
        }
        
        return null;
    }
}