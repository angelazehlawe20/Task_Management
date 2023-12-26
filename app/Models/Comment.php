<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $table='comments';
    protected $fillable=['user_id','task_id','content'];
    protected $casts=[
        'addition_date'=> 'datetime'
    ];

public function task()
    {
        return $this->belongsTo(Comment::class, 'task_id');
    }


}
