<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Domain;
class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Domain::factory()->count(50)->create();
    }
}
