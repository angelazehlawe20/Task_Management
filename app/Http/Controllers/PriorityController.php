<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GeneralTrait;
use App\Models\Priority;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Print_;

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
        $order=$request->input('order');
        $priorityColors = [
            'high' => '#FF0000',
            'medium' => '#FFFF00',
            'low' => '#00FF00'
        ];

        return $priorityColors[$order] ?? '#FFFFFF';
    }

    public function createPriority(Request $request)
{
    try {
        $validatedData = $request->validate([
            'description' => 'required|string',
            'order' => 'required|string|in:high,medium,low',
        ]);

        $color = $this->getColorForOrder($validatedData['order']);

        $priorityData = [
            'description' => $validatedData['description'],
            'order' => $validatedData['order'],
            'color_or_mark' => $color,
            'controller' => $this, 
        ];

        $priority = Priority::create($priorityData);

        return $this->ResponseTasks($priority, 'Priority created successfully', 201);
    } catch (ValidationException $e) {
        return $this->ResponseTasksErrors('Please ensure the accuracy of the provided information and fill in the required fields', 400);
    }
}


    public function updatePriority(Request $request, Priority $priority){
        try {
            $validatedData = $request->validate([
                'id' => 'integer|required|exists:priorities,id',
                'description' => 'string',
                'order' => 'string|in:high,medium,low',
                'color_or_mark' => 'string|in:#FF0000,#FFFF00,#00FF00'
            ]);
        } catch (ValidationException $e) {
            return $this->ResponseTasksErrors('Please ensure the accuracy of the provided information and fill in the required fields', 400);
        }
        $priority=Priority::find($validatedData['id']);

        $priority->update($request->except('id'));

        $priority->setVisible([
            'id',
            'description',
            'order',
            'color_or_mark']);

        return $this->ResponseTasks($priority,'Priority updated successfully', 200);
    }

    public function deletPriority(Request $request,Priority $priority){
        $prio_id=$request->input('id');
        $prioDel=Priority::find($prio_id);
        if(!$prioDel){
            return $this->ResponseTasksErrors('Priority not found',404);
        }
        $prioDel->delete();
        $priorities=Priority::all();
        return $this->ResponseTasks($priorities,'Priority deleted successfully',200);
    }



    }


