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

    const HIGH = 'high';
    const MEDIUM = 'medium';
    const LOW = 'low';

    const COLORS = [
        self::HIGH => '#FF0000',
        self::MEDIUM => '#FFFF00',
        self::LOW => '#00FF00',
    ];
}
