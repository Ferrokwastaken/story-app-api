<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Fiction',
            'genre' => 'Mystery',
        ]);

        Category::create([
            'name' => 'Non-Fiction',
            'genre' => 'Biography',
        ]);

        Category::create([
            'name' => 'Science Fiction',
            'genre' => 'Space Opera',
        ]);

        Category::create([
            'name' => 'Fantasy',
            'genre' => 'Epic Fantasy',
        ]);

        Category::create([
            'name' => 'Thriller',
            'genre' => 'Psychological Thriller',
        ]);

        Category::create([
            'name' => 'Historical Fiction',
            'genre' => 'Medieval',
        ]);

        Category::create([
            'name' => 'Romance',
            'genre' => 'Contemporary Romance',
        ]);

        Category::create([
            'name' => 'Horror',
            'genre' => 'Supernatural Horror',
        ]);

        Category::create([
            'name' => 'Young Adult',
            'genre' => 'Dystopian',
        ]);

        Category::create([
            'name' => "Children's Literature",
            'genre' => 'Picture Book',
        ]);

        Category::create([
            'name' => 'Poetry',
            'genre' => 'Sonnet',
        ]);

        Category::create([
            'name' => 'Drama',
            'genre' => 'Tragedy',
        ]);
    }
}
