<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Env extends Model
{
    /** @use HasFactory<\Database\Factories\EnvFactory> */
    use HasFactory;

    protected $fillable = [
        'content',
        'project_id'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
