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
        $priorities=Priority::query();
        switch($sortBy){
            case 'importance':
                $priorities->orderBy('order');
                break;
            case 'color':
                $priorities->orderBy('color_or_mark');
                break;
            case 'name':
                $priorities->orderBy('name');
                break;
            default:
            return $this->ResponseTasksErrors('Invalid sorting parameter', 400);
            break;
        }
        $sortedPri=$priorities->get();
        return $this->ResponseTasks($sortedPri,'All priorities by '.$sortBy.':',200);

    }

    public function updatePriority(Request $request,Priority $priority){
        $priority_id=$request->input('id');
        $priorityUpd = Priority::find($priority_id);

        if (!$priority) {
            return response()->json(['message' => 'Priority not found'], 404);
        }

        $priority->name = $request->input('name'); // تحديث الاسم
        $priority->description = $request->input('description'); // تحديث الوصف

        $priority->save();

        return response()->json(['message' => 'Priority updated successfully'], 200);
    }
    }


}
