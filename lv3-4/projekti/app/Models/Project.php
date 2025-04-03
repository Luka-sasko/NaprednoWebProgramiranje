<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'description', 'price', 'completed_tasks', 'start_date', 'end_date'];

    // Voditelj projekta
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Članovi projektnog tima
    public function teamMembers()
    {
        return $this->belongsToMany(User::class, 'project_team_members', 'project_id', 'user_id')
                    ->withTimestamps();
    }
}