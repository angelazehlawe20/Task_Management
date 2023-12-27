<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Priority;
use Dotenv\Exception\ValidationException;
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

    protected function getColorForOrder(Request $request)
{
    $order = $request->input('order');
    $priorityColors = [
        'high' => '#FF0000',
        'medium' => '#FFFF00',
        'low' => '#00FF00'
    ];

    if (array_key_exists($order, $priorityColors)) {
        return $priorityColors[$order];
    }
    return $this->ResponseTasksErrors('Color of this '.$order.' priority not found',404);
}
public function createPriority(Request $request)
{
    try {
        $validatedData = $request->validate([
            'description' => 'required|string',
            'order' => 'required|string|in:high,medium,low',
        ]);

        $order = $validatedData['order'];
        $request=$request;
        $color = $this->getColorForOrder($request,$order);

        $priorityData = [
            'description' => $validatedData['description'],
            'order' => $order,
            'color_or_mark' => $color,
        ];

        $priority = Priority::create($priorityData);

        return $this->ResponseTasks($priority, 'Priority created successfully', 201);
    } catch (ValidationException $e) {
        return $this->ResponseTasksErrors('Please ensure the accuracy of the provided information and fill in the required fields', 400);
    }
}




public function updatePriority(Request $request, Priority $priority)
{
    try {
        $validatedData = $request->validate([
            'id' => 'integer|required|exists:priorities,id',
            'description' => 'string',
            'order' => 'string|in:high,medium,low',
        ]);

        $color = $this->getColorForOrder($request);

        $prio_id=Priority::find($validatedData['id']);
        $prio_id->update([
            'description' => $request->input('description', $prio_id->description),
            'order' => $request->input('order', $prio_id->order),
            'color_or_mark' => $color,
        ]);

        $priority->setVisible(['id', 'description', 'order', 'color_or_mark']);

        return $this->ResponseTasks($prio_id, 'Priority updated successfully', 200);
    } catch (ValidationException $e) {
        return $this->ResponseTasksErrors('Please ensure the accuracy of the provided information and fill in the required fields', 400);
    }
}

public function deletPriority(Request $request, Priority $priority){
    $prio_id = $request->input('id');
    $prioDel = Priority::find($prio_id);
    if (!$prioDel) {
        return $this->ResponseTasksErrors('Priority not found', 404);
    }
    $prioDel->delete();
    $priorities = Priority::all();
    return $this->ResponseTasks($priorities, 'Priority deleted successfully', 200);
}



    }


