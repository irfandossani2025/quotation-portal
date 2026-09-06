<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function adminOnly(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403);
    }

    public function index()
    {
        $this->adminOnly();

        return view('products.index', ['products' => Product::latest()->paginate(24)]);
    }

    public function store(Request $request)
    {
        $this->adminOnly();
        $data = $request->validate([
            'supplier' => ['required', 'max:255'], 'sku' => ['nullable', 'max:255'],
            'name' => ['required', 'max:255'], 'description' => ['nullable'],
            'category' => ['nullable', 'max:255'], 'source_url' => ['nullable', 'url', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = Storage::disk('public')->url($request->file('image')->store('products', 'public'));
        }

        Product::create([
            'supplier' => $data['supplier'], 'source_key' => 'manual-'.Str::uuid(),
            'source_url' => $data['source_url'] ?? 'https://manual.local/', 'sku' => $data['sku'] ?? null,
            'name' => $data['name'], 'description' => $data['description'] ?? null,
            'image_url' => $data['image_url'] ?? null, 'category' => $data['category'] ?? null,
        ]);

        return back()->with('success', 'Product added to the catalog.');
    }
}