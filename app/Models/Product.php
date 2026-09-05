<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = ['active' => 'boolean', 'source_updated_at' => 'datetime'];
}
