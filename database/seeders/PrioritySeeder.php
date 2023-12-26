<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Priority::create([
            'name' => 'High Priority',
            'description' => 'This is a high priority task',
            'order' => Priority::HIGH,
            'color_or_mark' => Priority::COLORS[Priority::HIGH]
        ]);

        Priority::create([
            'name' => 'Medium Priority',
            'description' => 'This is a medium priority task',
            'order' => Priority::MEDIUM,
            'color_or_mark' => Priority::COLORS[Priority::MEDIUM]
        ]);

        Priority::create([
            'name' => 'Low Priority',
            'description' => 'This is a low priority task',
            'order' => Priority::LOW,
            'color_or_mark' => Priority::COLORS[Priority::LOW]
        ]);
    }
}
