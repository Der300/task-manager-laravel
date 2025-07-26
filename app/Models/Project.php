<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    public $table = 'projects';
    protected $guarded=[];

    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory, SoftDeletes;

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function users()
    { //moi quan he many-to-many project_user table
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')->withTimestamps();
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function clientUser()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function issueType()
    {
        return $this->belongsTo(IssueType::class, 'issue_type_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
