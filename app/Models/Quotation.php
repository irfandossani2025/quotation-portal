<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quotation_date' => 'date', 'valid_until' => 'date',
        'subtotal' => 'decimal:3', 'vat_rate' => 'decimal:2', 'vat_amount' => 'decimal:3', 'total' => 'decimal:3',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    public function recalculate(): void
    {
        $subtotal = $this->items()->sum('line_total');
        $vat = round((float) $subtotal * ((float) $this->vat_rate / 100), 3);
        $this->update(['subtotal' => $subtotal, 'vat_amount' => $vat, 'total' => (float) $subtotal + $vat]);
    }
}
