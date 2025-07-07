<?php

namespace App\Models\Project;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    // thiet lap lai duong dan file factory do Project trong App\Models\Project mac dinh factory lay duong dan Database\Factories\Project
    protected static function newFactory()
    {
        return ProjectFactory::new();
    }
}
