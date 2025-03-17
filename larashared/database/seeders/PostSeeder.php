<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \App\Models\Post::factory()->count(10)->create(); // Creates 10 posts
    }
}
