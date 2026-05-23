<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pendidikan & Pengajaran', 'color' => '#3B82F6', 'icon' => 'book-open'],
            ['name' => 'Penelitian & Publikasi', 'color' => '#10B981', 'icon' => 'beaker'],
            ['name' => 'Pengabdian Masyarakat', 'color' => '#F59E0B', 'icon' => 'heart'],
            ['name' => 'Magang & MBKM', 'color' => '#8B5CF6', 'icon' => 'briefcase'],
            ['name' => 'Beasiswa & Pertukaran', 'color' => '#EC4899', 'icon' => 'academic-cap'],
            ['name' => 'Pengembangan SDM', 'color' => '#06B6D4', 'icon' => 'users'],
            ['name' => 'Kerjasama Industri', 'color' => '#EF4444', 'icon' => 'office-building'],
            ['name' => 'Teknologi & Inovasi', 'color' => '#6366F1', 'icon' => 'chip'],
        ];

        foreach ($categories as $i => $category) {
            Category::create(array_merge($category, ['sort_order' => $i + 1]));
        }
    }
}
