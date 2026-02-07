<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlogPost;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogPosts = [
            [
                'title' => 'Understanding RTP: The Key to Smart Casino Gaming',
                'excerpt' => 'Learn what Return to Player (RTP) means and how to use RTP data to make informed gaming decisions.',
                'content' => 'Return to Player (RTP) is one of the most important concepts in online casino gaming. This comprehensive guide explains what RTP means, how it affects your gaming experience, and how to use RTP data strategically to maximize your chances of winning...',
                'author_name' => 'Alex Thompson',
                'author_role' => 'Gaming Analyst',
                'tags' => 'RTP, casino mathematics, gaming strategy, odds',
                'read_time' => 8,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Top 10 High RTP Slots for 2024',
                'excerpt' => 'Discover the highest RTP slot games available this year and learn why RTP matters for your gaming strategy.',
                'content' => 'When it comes to slot gaming, RTP (Return to Player) is your best friend. This article explores the top 10 slot games with the highest RTP percentages available in 2024...',
                'author_name' => 'Sarah Chen',
                'author_role' => 'Slot Specialist',
                'tags' => 'slots, high RTP, 2024, slot reviews',
                'read_time' => 6,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Bankroll Management: How to Play Longer and Win More',
                'excerpt' => 'Master the art of bankroll management with proven strategies that help you extend your gaming sessions and protect your funds.',
                'content' => 'Effective bankroll management is the foundation of successful casino gaming. Without proper money management, even the best strategies will fail. This comprehensive guide covers essential bankroll management techniques...',
                'author_name' => 'Michael Rodriguez',
                'author_role' => 'Strategy Expert',
                'tags' => 'bankroll management, money management, gaming strategy',
                'read_time' => 10,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'The Complete Guide to Online Casino Bonuses',
                'excerpt' => 'Everything you need to know about casino bonuses, from welcome offers to free spins and loyalty rewards.',
                'content' => 'Casino bonuses can significantly boost your gaming experience and bankroll. This comprehensive guide explains all types of casino bonuses, how to claim them, and the terms and conditions you need to understand...',
                'author_name' => 'Emma Wilson',
                'author_role' => 'Bonus Specialist',
                'category' => 'promotions',
                'tags' => 'casino bonuses, welcome bonus, free spins, loyalty program',
                'read_time' => 12,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Responsible Gaming: Playing Smart and Staying Safe',
                'excerpt' => 'Learn about responsible gaming practices and how to maintain a healthy relationship with casino entertainment.',
                'content' => 'Responsible gaming is essential for ensuring that casino entertainment remains enjoyable and safe. This article covers important topics like setting limits, recognizing problem signs, and using available tools for safe gaming...',
                'author_name' => 'Dr. James Mitchell',
                'author_role' => 'Gaming Psychologist',
                'category' => 'educational',
                'tags' => 'responsible gaming, gambling safety, gaming addiction',
                'read_time' => 7,
                'is_featured' => false,
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'How Real-Time RTP Tracking Changes Everything',
                'excerpt' => 'Discover how real-time RTP tracking gives you an edge in casino gaming and why it\'s revolutionizing the industry.',
                'content' => 'Real-time RTP tracking represents a paradigm shift in how players approach casino gaming. By monitoring RTP data in real-time, players can make more informed decisions about when to play, which games to choose, and how to optimize their strategy...',
                'author_name' => 'David Park',
                'author_role' => 'Technology Analyst',
                'category' => 'tools-tutorials',
                'tags' => 'RTP tracking, real-time data, gaming tools, analytics',
                'read_time' => 9,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now(),
            ],
        ];

        foreach ($blogPosts as $post) {
            BlogPost::create([
                'title' => $post['title'],
                'slug' => Str::slug($post['title']),
                'excerpt' => $post['excerpt'],
                'content' => $post['content'],
                'author_name' => $post['author_name'],
                'author_role' => $post['author_role'],
                'tags' => $post['tags'],
                'read_time' => $post['read_time'],
                'is_featured' => $post['is_featured'],
                'is_published' => $post['is_published'],
                'published_at' => $post['published_at'],
                'view_count' => rand(50, 500),
            ]);
        }
    }
}
