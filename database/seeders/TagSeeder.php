<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Fiction',
            'Science Fiction',
            'Fantasy',
            'Mystery',
            'Thriller',
            'Horror',
            'Romance',
            'Historical Fiction',
            'Young Adult',
            "Children's Literature",
            'Contemporary',
            'Dystopian',
            'Adventure',
            'Crime',
            'Suspense',
            'Paranormal',
            'Supernatural',
            'Urban Fantasy',
            'Steampunk',
            'Cyberpunk',
        ];

        foreach ($tags as $tag) {
            Tag::create([
            'name' => $tag,
            ]);
        }
    }
}
