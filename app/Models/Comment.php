<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $table='comments';
    protected $fillable=['user_id','task_id','content','addition_date'];

public function task()
    {
        return $this->belongsTo(Comment::class, 'task_id');
    }
public function comment()
   {
     return $this->belongsTo(Comment::class,'addition_date');
   }

}
