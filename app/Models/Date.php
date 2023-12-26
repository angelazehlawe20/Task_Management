<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    use HasFactory;
    protected $table='dates';
    protected $fillable=['date','day','month','year','time'];

public function comments()
{
    return $this->hasMany(Comment::class, 'addition_date_id');
}
}
