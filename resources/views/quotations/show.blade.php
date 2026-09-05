@extends('layout')
@section('content')
<div class="page-head"><div><div class="eyebrow">{{ $quotation->company->trading_name }}</div><h1>{{ $quotation->number }}</h1><p>Prepared by {{ $quotation->creator->name }} · {{ $quotation->creator->phone }} · {{ $quotation->creator->email }}</p></div>
<div class="actions">@if(in_array($quotation->status,['priced','issued']))<a class="btn secondary" href="{{ route('quotations.pdf',$quotation) }}">↓ Download PDF</a>@endif @if($quotation->status==='priced')<form method="post" action="{{ route('quotations.issue',$quotation) }}">@csrf<button class="btn green">Issue quotation</button></form>@endif</div></div>
<section class="panel quote-card" style="border-top:5px solid {{ $quotation->company->accent }}">
<div class="quote-meta"><img src="{{ asset($quotation->company->logo_path) }}" alt="{{ $quotation->company->trading_name }}"><div><strong>QUOTATION</strong><br><span class="muted">Date {{ $quotation->quotation_date->format('d M Y') }}<br>Valid until {{ $quotation->valid_until->format('d M Y') }}</span></div><div><strong>Prepared for</strong><br>{{ $quotation->customer_name }}<br>{{ $quotation->customer_company }}<br><span class="muted">{{ $quotation->customer_email }}</span></div></div>
<div class="table-wrap" style="margin-top:20px"><table><thead><tr><th>S.No</th><th>Photo</th><th>Product description</th><th>Quantity</th><th>Price per unit</th><th>Total price</th></tr></thead><tbody>
@foreach($quotation->items as $item)<tr><td>{{ $loop->iteration }}</td><td>@if($item->photo_url)<img src="{{ $item->photo_url }}" style="width:58px;height:58px;object-fit:contain" alt="">@else—@endif</td><td><strong>{{ $item->description }}</strong><br><small class="muted">{{ $item->product?->supplier }}</small></td><td>{{ number_format($item->quantity,3) }}</td><td>{{ $item->unit_price === null ? 'Awaiting price' : 'OMR '.number_format($item->unit_price,3) }}</td><td>OMR {{ number_format($item->line_total,3) }}</td></tr>@endforeach
</tbody></table></div>
<div class="quote-total"><div><span>Subtotal</span><strong>OMR {{ number_format($quotation->subtotal,3) }}</strong></div><div><span>VAT ({{ number_format($quotation->vat_rate,0) }}%)</span><strong>OMR {{ number_format($quotation->vat_amount,3) }}</strong></div><div class="grand"><span>Total</span><span>OMR {{ number_format($quotation->total,3) }}</span></div></div>
</section>
@endsection
