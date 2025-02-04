<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Romantic'],
            ['name' => 'Family Gathering'],
            ['name' => 'Friends Meetup'],
            ['name' => 'Business Dining'],
            ['name' => 'Casual Dining'],
            ['name' => 'Fine Dining']
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(['name' => $tag['name']], $tag);
        }
    }
}
