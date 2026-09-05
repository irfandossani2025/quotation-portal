<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = [];

    protected $casts = ['vat_rate' => 'decimal:2'];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
}
