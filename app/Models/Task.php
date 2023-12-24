<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $table='tasks';
    protected $fillable=['user_id','priority_id','title','description','Status','due_date'];


    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
