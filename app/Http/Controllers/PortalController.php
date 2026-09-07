<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Product;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortalController extends Controller
{
    private function officeOnly(): void
    {
        abort_if(auth()->user()->role === 'pricing', 403);
    }

    public function dashboard()
    {
        $this->officeOnly();

        return view('dashboard', [
            'counts' => Quotation::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'quotations' => Quotation::with(['company', 'creator'])->latest()->limit(8)->get(),
        ]);
    }

    public function index(Request $request)
    {
        $this->officeOnly();
        $query = Quotation::with(['company', 'creator'])->latest();
        if ($request->filled('q')) {
            $query->where(fn ($q) => $q->where('number', 'like', '%'.$request->q.'%')->orWhere('customer_name', 'like', '%'.$request->q.'%'));
        }

        return view('quotations.index', ['quotations' => $query->paginate(20)->withQueryString()]);
    }

    public function create(Request $request)
    {
        $this->officeOnly();
        $products = Product::where('active', true)->when($request->q, fn ($q, $term) => $q->where(fn ($s) => $s->where('name', 'like', "%$term%")->orWhere('sku', 'like', "%$term%")))->limit(24)->get();

        return view('quotations.create', ['companies' => Company::all(), 'products' => $products]);
    }

    public function store(Request $request)
    {
        $this->officeOnly();
        $data = $request->validate([
            'company_id' => ['required', 'exists:companies,id'], 'customer_name' => ['required', 'max:255'],
            'customer_company' => ['nullable', 'max:255'], 'customer_email' => ['nullable', 'email'],
            'customer_phone' => ['nullable', 'max:50'], 'customer_address' => ['nullable'], 'subject' => ['nullable', 'max:255'],
            'valid_until' => ['required', 'date'], 'notes' => ['nullable'], 'product_id' => ['nullable', 'array'],
            'quantity' => ['nullable', 'array'], 'product_id.*' => ['required', 'exists:products,id'],
            'quantity.*' => ['required', 'numeric', 'gt:0'], 'custom_product_name' => ['nullable', 'array'],
            'custom_product_name.*' => ['nullable', 'max:255'], 'custom_supplier' => ['nullable', 'array'],
            'custom_supplier.*' => ['nullable', 'max:255'], 'custom_quantity' => ['nullable', 'array'],
            'custom_quantity.*' => ['nullable', 'numeric', 'gt:0'], 'custom_image' => ['nullable', 'array'],
            'custom_image.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $catalogIds = array_filter($data['product_id'] ?? []);
        $customNames = array_filter($data['custom_product_name'] ?? [], fn ($name) => filled($name));
        abort_if(count($catalogIds) + count($customNames) < 1, 422, 'Select a catalog product or add a product manually.');
        $quotation = DB::transaction(function () use ($data) {
            $company = Company::lockForUpdate()->findOrFail($data['company_id']);
            $year = now()->year;
            DB::table('quotation_sequences')->insertOrIgnore(['company_id' => $company->id, 'year' => $year, 'next_number' => 1, 'created_at' => now(), 'updated_at' => now()]);
            $sequence = DB::table('quotation_sequences')->where('company_id', $company->id)->where('year', $year)->lockForUpdate()->first();
            $last = $sequence->next_number;
            DB::table('quotation_sequences')->where('id', $sequence->id)->update(['next_number' => $last + 1, 'updated_at' => now()]);
            $quotation = Quotation::create([
                'company_id' => $company->id, 'created_by' => auth()->id(), 'number' => sprintf('%s-%d-%04d', $company->quotation_prefix, $year, $last),
                'customer_name' => $data['customer_name'], 'customer_company' => $data['customer_company'] ?? null,
                'customer_email' => $data['customer_email'] ?? null, 'customer_phone' => $data['customer_phone'] ?? null,
                'customer_address' => $data['customer_address'] ?? null, 'subject' => $data['subject'] ?? null,
                'quotation_date' => today(), 'valid_until' => $data['valid_until'], 'status' => 'pricing',
                'vat_rate' => $company->vat_rate, 'notes' => $data['notes'] ?? null,
            ]);
            foreach ($data['product_id'] ?? [] as $index => $productId) {
                $product = Product::findOrFail($productId);
                $quotation->items()->create(['product_id' => $product->id, 'sort_order' => $index + 1,
                    'description' => $product->name, 'photo_url' => $product->image_url, 'quantity' => $data['quantity'][$productId]]);
            }
            foreach ($data['custom_product_name'] ?? [] as $index => $name) {
                if (blank($name)) {
                    continue;
                }
                $imageUrl = null;
                if (isset($data['custom_image'][$index])) {
                    $imageUrl = Storage::disk('public')->url($data['custom_image'][$index]->store('products', 'public'));
                }
                $product = Product::create(['supplier' => $data['custom_supplier'][$index] ?? 'Manual', 'source_key' => 'manual-'.Str::uuid(),
                    'source_url' => 'https://manual.local/', 'name' => $name, 'image_url' => $imageUrl, 'active' => true]);
                $quotation->items()->create(['product_id' => $product->id, 'sort_order' => count($data['product_id'] ?? []) + $index + 1,
                    'description' => $product->name, 'photo_url' => $imageUrl, 'quantity' => $data['custom_quantity'][$index] ?? 1]);
            }

            return $quotation;
        });

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation sent to Dubai for pricing.');
    }

    public function show(Quotation $quotation)
    {
        $this->officeOnly();

        return view('quotations.show', ['quotation' => $quotation->load(['company', 'creator', 'items.product'])]);
    }

    public function issue(Quotation $quotation)
    {
        $this->officeOnly();
        abort_unless($quotation->status === 'priced', 422, 'All prices must be completed first.');
        $quotation->update(['status' => 'issued']);

        return back()->with('success', 'Quotation issued and locked.');
    }

    public function pdf(Quotation $quotation)
    {
        $this->officeOnly();
        abort_unless(in_array($quotation->status, ['priced', 'issued']), 422, 'Pricing must be completed before PDF generation.');
        $quotation->load(['company', 'creator', 'items']);

        return Pdf::loadView('quotations.pdf', compact('quotation'))->setPaper('a4')->setOption('isRemoteEnabled', true)->download($quotation->number.'.pdf');
    }
}
