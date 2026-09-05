@extends('layout')
@section('content')
<div class="page-head"><div><a class="muted" href="{{ route('pricing.index') }}">← All requests</a><div class="eyebrow" style="margin-top:14px">{{ $quotation->company->trading_name }}</div><h1>{{ $quotation->number }}</h1><p>{{ $quotation->customer_name }} · {{ $quotation->items->count() }} products</p></div></div>
<form method="post" action="{{ route('pricing.update',$quotation) }}">@csrf @method('PUT')
@foreach($quotation->items as $item)<article class="price-item">
    @if($item->photo_url)<img src="{{ $item->photo_url }}" alt="">@else<span class="photo-placeholder">◇</span>@endif
    <div><strong>{{ $loop->iteration }}. {{ $item->description }}</strong><br><span class="muted">{{ $item->product?->supplier }} · Qty {{ number_format($item->quantity,3) }}</span></div>
    <div class="price-input"><label for="price-{{ $item->id }}">PRICE PER UNIT</label><div class="money"><span>OMR</span><input id="price-{{ $item->id }}" type="number" inputmode="decimal" step="0.001" min="0" name="prices[{{ $item->id }}]" value="{{ old('prices.'.$item->id,$item->unit_price) }}" placeholder="0.000" required></div></div>
</article>@endforeach
<div class="sticky-save"><button class="btn green full">Save all prices & send to Muscat</button></div></form>
@endsection
