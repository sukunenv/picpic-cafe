<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['title', 'subtitle', 'tag', 'image', 'gradient_start', 'gradient_end', 'type', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
