<?php

namespace Database\Seeders;

use App\Models\ReferenceProject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReferenceProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['title' => ['tr' => 'Belek Otel Havuz Projesi', 'en' => 'Belek Hotel Pool Project', 'ar' => 'مشروع مسبح فندق بيليك'], 'location' => ['tr' => 'Antalya, Türkiye', 'en' => 'Antalya, Turkey', 'ar' => 'أنطاليا، تركيا']],
            ['title' => ['tr' => 'Özel Villa Havuzu', 'en' => 'Private Villa Pool', 'ar' => 'مسبح فيلا خاصة'], 'location' => ['tr' => 'İstanbul, Türkiye', 'en' => 'Istanbul, Turkey', 'ar' => 'إسطنبول، تركيا']],
        ];

        foreach ($projects as $index => $data) {
            $slug = Str::slug($data['title']['tr']);
            $project = ReferenceProject::query()->firstOrNew(['slug' => $slug]);
            $project->title = $data['title'];
            $project->location = $data['location'];
            $project->is_active = true;
            $project->is_featured = true;
            $project->sort_order = $index;
            $project->save();
        }
    }
}
