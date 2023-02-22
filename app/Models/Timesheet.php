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
        'schedule',
        'manager_id',
        'status'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($timesheet) { // before delete() method call this
            $timesheet->tasks()->delete();
            // do the rest of the cleanup...
        });
    }
}
