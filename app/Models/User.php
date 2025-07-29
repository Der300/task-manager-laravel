<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;
    public $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'position',
        'department',
        'status',
        'role',
        'image',
    ];

    public function projects()
    { //moi quan he many-to-many project_user table
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id')->withTimestamps();
    }

    public function files()
    {
        return $this->hasMany(File::class, 'uploaded_by');
    }
}
