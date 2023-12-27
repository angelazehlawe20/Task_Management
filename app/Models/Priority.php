<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;
    protected $table='priorities';
    protected $fillable=['name','description','order','color_or_mark'];

    public function priority()
    {
        return $this->hasMany(Task::class,'priority_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($priority) {
            $controller = $priority->controller; 
            $color = $controller->getColorForOrder($priority->order);

            $priority->color_or_mark = $color;
        });
    }

}
