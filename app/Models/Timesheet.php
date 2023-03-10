<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'difficult',
        'user_id',
        'schedule'
    ];

    public function tasks()
    {
        return $this->hasMany(Line::class);
    }
}
