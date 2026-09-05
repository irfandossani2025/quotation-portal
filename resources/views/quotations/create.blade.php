@extends('layout')
@section('content')
<div class="page-head"><div><div class="eyebrow">New quotation</div><h1>Build the product request</h1><p>Select products now; Dubai will add the final unit prices from mobile.</p></div></div>
<form method="post" action="{{ route('quotations.store') }}">@csrf<div class="grid-2">
<section class="panel form-panel"><h2 style="margin-top:0">Customer & brand</h2>
    <label class="field"><span>Issuing company</span><select name="company_id" required>@foreach($companies as $company)<option value="{{ $company->id }}">{{ $company->legal_name }} — {{ $company->trading_name }}</option>@endforeach</select></label>
    <div class="grid-2"><label class="field"><span>Contact name</span><input name="customer_name" value="{{ old('customer_name') }}" required></label><label class="field"><span>Company</span><input name="customer_company" value="{{ old('customer_company') }}"></label></div>
    <div class="grid-2"><label class="field"><span>Email</span><input type="email" name="customer_email"></label><label class="field"><span>Phone</span><input name="customer_phone"></label></div>
    <label class="field"><span>Address</span><textarea name="customer_address" rows="2"></textarea></label>
    <label class="field"><span>Quotation subject</span><input name="subject" placeholder="Corporate gifts for annual event"></label>
    <label class="field"><span>Valid until</span><input type="date" name="valid_until" value="{{ now()->addDays(30)->toDateString() }}" required></label>
    <label class="field"><span>Notes</span><textarea name="notes" rows="3"></textarea></label>
    <button class="btn green full">Send to Dubai for pricing →</button>
</section>
<section class="panel form-panel"><h2 style="margin-top:0">Choose products</h2><p class="muted">Showing catalog matches from all four approved suppliers.</p>
    <div class="product-grid">@foreach($products as $product)<label class="product-card">
        <input type="checkbox" name="product_id[]" value="{{ $product->id }}">
        @if($product->image_url)<img src="{{ $product->image_url }}" alt="">@else<span class="photo-placeholder">◇</span>@endif
        <span><strong>{{ $product->name }}</strong><small>{{ $product->supplier }} · {{ $product->sku }}</small></span>
        <span class="qty">Quantity <input type="number" step="0.001" min="0.001" name="quantity[{{ $product->id }}]" value="1"></span>
    </label>@endforeach</div>
</section></div></form>
@endsection
