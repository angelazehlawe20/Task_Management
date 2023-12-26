<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends Controller

{
    use GeneralTrait;

    public function getAll(Request $request,Priority $priority){
        $sortBy=$request->input('sort_by','order');
        $priorities=Priority::with(['order']);
        switch($sortBy){
            case 'priority':
                $priorities->orderBy('order');
                break;
            case 'color':
                $priorities->orderBy('color_or_mark');
                break;
            default:
            return $this->ResponseTasksErrors('Invalid sorting parameter', 400);
            break;
        }
        $sortedPri=$priorities->get();
        return $this->ResponseTasks($sortedPri,'All priorities by '.$sortBy.':',200);

    }


    public function create(){

        $priority = Priority::create([
            'name' => 'High Priority',
            'description' => 'This is a high priority task',
            'order' => Priority::HIGH,
            'color_or_mark' => Priority::COLORS[Priority::HIGH]]);
        }
}
