@extends('layout')
@section('content')
<div class="page-head"><div><div class="eyebrow">Muscat office</div><h1>Quotation overview</h1><p>Track every quote from product selection to final issue.</p></div><a class="btn green" href="{{ route('quotations.create') }}">＋ New quotation</a></div>
<div class="stats">
    <div class="stat"><span>All quotations</span><strong>{{ $counts->sum() }}</strong></div>
    <div class="stat"><span>Awaiting Dubai</span><strong>{{ $counts['pricing'] ?? 0 }}</strong></div>
    <div class="stat"><span>Ready to issue</span><strong>{{ $counts['priced'] ?? 0 }}</strong></div>
    <div class="stat"><span>Issued</span><strong>{{ $counts['issued'] ?? 0 }}</strong></div>
</div>
<section class="panel"><div class="panel-head"><h2>Recent quotations</h2><a href="{{ route('quotations.index') }}">View all →</a></div>
<div class="table-wrap"><table><thead><tr><th>Quotation</th><th>Customer</th><th>Company</th><th>Prepared by</th><th>Status</th></tr></thead><tbody>
@forelse($quotations as $q)<tr><td><a href="{{ route('quotations.show',$q) }}"><strong>{{ $q->number }}</strong></a></td><td>{{ $q->customer_name }}</td><td><span class="company-dot" style="background:{{ $q->company->accent }}"></span>{{ $q->company->trading_name }}</td><td>{{ $q->creator->name }}</td><td><span class="badge {{ $q->status }}">{{ $q->status }}</span></td></tr>
@empty<tr><td colspan="5" class="muted">No quotations yet. Create the first one.</td></tr>@endforelse
</tbody></table></div></section>
@endsection
