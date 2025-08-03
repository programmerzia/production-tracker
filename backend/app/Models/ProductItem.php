<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'name',
        'version',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
