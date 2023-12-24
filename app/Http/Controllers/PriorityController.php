<?php

namespace App\Http\Controllers;

use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    public function create(){


        $priority = Priority::create([
            'name' => 'High Priority',
            'description' => 'This is a high priority task',
            'order' => Priority::HIGH,
            'color_or_mark' => Priority::COLORS[Priority::HIGH]]);
        }
}
