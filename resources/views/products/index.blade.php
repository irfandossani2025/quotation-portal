@extends('layout')
@section('content')
<div class="page-head"><div><div class="eyebrow">Catalog administration</div><h1>Products</h1><p>Add products manually for quotation requests, including their product image.</p></div></div>
<div class="grid-2">
    <section class="panel form-panel"><h2 style="margin-top:0">Add a product</h2>
        <form method="post" action="{{ route('products.store') }}" enctype="multipart/form-data">@csrf
            <label class="field"><span>Product name</span><input name="name" value="{{ old('name') }}" required></label>
            <div class="grid-2"><label class="field"><span>Supplier</span><input name="supplier" value="{{ old('supplier') }}" required></label><label class="field"><span>SKU <small>(optional)</small></span><input name="sku" value="{{ old('sku') }}"></label></div>
            <div class="grid-2"><label class="field"><span>Category</span><input name="category" value="{{ old('category') }}"></label><label class="field"><span>Product image</span><input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label></div>
            <label class="field"><span>Description</span><textarea name="description" rows="3">{{ old('description') }}</textarea></label>
            <label class="field"><span>Source URL <small>(optional)</small></span><input type="url" name="source_url" value="{{ old('source_url') }}" placeholder="https://supplier.example/product"></label>
            <button class="btn green full">Add product</button>
        </form>
    </section>
    <section class="panel"><div class="panel-head"><h2>Catalog</h2><span class="muted">{{ $products->total() }} products</span></div>
        <div class="table-wrap"><table><thead><tr><th>Product</th><th>Supplier</th><th>Category</th></tr></thead><tbody>
        @forelse($products as $product)<tr><td>@if($product->image_url)<img class="table-product-image" style="width:54px;height:54px;border-radius:10px;object-fit:contain;background:#edf2ef;vertical-align:middle" src="{{ $product->image_url }}" alt="">@else<span class="photo-placeholder">◇</span>@endif <strong>{{ $product->name }}</strong><br><small>{{ $product->sku ?: 'No SKU' }}</small></td><td>{{ $product->supplier }}</td><td>{{ $product->category ?: '—' }}</td></tr>
        @empty<tr><td colspan="3" class="muted">No products yet. Add the first product.</td></tr>@endforelse
        </tbody></table></div>
        @if($products->hasPages())<div class="pagination">{{ $products->links() }}</div>@endif
    </section>
</div>
@endsection