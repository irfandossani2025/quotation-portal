<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PricingController extends Controller
{
    private function pricingOnly(): void
    {
        abort_unless(in_array(auth()->user()->role, ['pricing', 'admin']), 403);
    }

    public function index()
    {
        $this->pricingOnly();

        return view('pricing.index', ['quotations' => Quotation::with(['company', 'creator', 'items'])->whereIn('status', ['pricing', 'priced'])->latest()->get()]);
    }

    public function edit(Quotation $quotation)
    {
        $this->pricingOnly();
        abort_unless(in_array($quotation->status, ['pricing', 'priced']), 404);

        return view('pricing.edit', ['quotation' => $quotation->load(['company', 'creator', 'items.product'])]);
    }

    public function update(Request $request, Quotation $quotation)
    {
        $this->pricingOnly();
        abort_unless(in_array($quotation->status, ['pricing', 'priced']), 422);
        $data = $request->validate(['prices' => ['required', 'array'], 'prices.*' => ['required', 'numeric', 'min:0']]);
        DB::transaction(function () use ($data, $quotation) {
            foreach ($quotation->items as $item) {
                abort_unless(array_key_exists($item->id, $data['prices']), 422);
                $price = round((float) $data['prices'][$item->id], 3);
                $item->update(['unit_price' => $price, 'line_total' => round($price * (float) $item->quantity, 3), 'priced_by' => auth()->id(), 'priced_at' => now()]);
            }
            $quotation->update(['status' => 'priced']);
            $quotation->recalculate();
        });

        return redirect()->route('pricing.index')->with('success', 'Prices saved. Muscat can now issue the quotation.');
    }
}
