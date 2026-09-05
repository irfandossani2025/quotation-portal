@extends('layout')
@section('content')
<div class="page-head"><div><div class="eyebrow">Sales workspace</div><h1>Quotations</h1><p>Search, check pricing, and issue customer-ready PDFs.</p></div><a class="btn green" href="{{ route('quotations.create') }}">＋ New quotation</a></div>
<section class="panel"><div class="panel-head"><form style="display:flex;gap:8px;width:min(500px,100%)"><input class="search" name="q" value="{{ request('q') }}" placeholder="Search number or customer"><button class="btn secondary">Search</button></form></div>
<div class="table-wrap"><table><thead><tr><th>Quotation</th><th>Customer</th><th>Brand</th><th>Total</th><th>Status</th></tr></thead><tbody>
@forelse($quotations as $q)<tr><td><a href="{{ route('quotations.show',$q) }}"><strong>{{ $q->number }}</strong><br><small>{{ $q->quotation_date->format('d M Y') }}</small></a></td><td>{{ $q->customer_name }}</td><td>{{ $q->company->trading_name }}</td><td>OMR {{ number_format($q->total,3) }}</td><td><span class="badge {{ $q->status }}">{{ $q->status }}</span></td></tr>@empty<tr><td colspan="5">No results.</td></tr>@endforelse
</tbody></table></div></section><div style="margin-top:18px">{{ $quotations->links() }}</div>
@endsection
