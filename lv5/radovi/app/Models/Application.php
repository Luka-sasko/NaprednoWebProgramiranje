<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Application.php
class Application extends Model
{
    protected $fillable = [
        'student_id',
        'task_id',
        'priority',
        'accepted' // Include if used elsewhere (e.g., in accept method)
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
