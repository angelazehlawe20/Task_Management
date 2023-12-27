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
        $order = $priority->order;

        $priorityColors = [
            'high' => '#FF0000',
            'medium' => '#FFFF00',
            'low' => '#00FF00'
        ];

        if (array_key_exists($order, $priorityColors)) {
            $color = $priorityColors[$order];
            $priority->color_or_mark = $color;
        } else {
            $priority->color_or_mark = '#FFFFFF';
        }
    });

}
}
