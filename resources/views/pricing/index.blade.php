@extends('layout')
@section('content')
<div class="page-head"><div><div class="eyebrow">Dubai price desk</div><h1>Prices needed</h1><p>Tap a request, enter each unit price, and send it back to Muscat.</p></div></div>
<div class="pricing-list">@forelse($quotations as $q)<article class="pricing-job"><div><span class="badge {{ $q->status }}">{{ $q->status==='pricing'?'Needs prices':'Completed' }}</span><h3>{{ $q->number }} · {{ $q->customer_name }}</h3><small class="muted">{{ $q->items->count() }} products · requested by {{ $q->creator->name }}</small></div><a class="btn {{ $q->status==='pricing'?'green':'secondary' }}" href="{{ route('pricing.edit',$q) }}">{{ $q->status==='pricing'?'Add prices':'Review' }} →</a></article>
@empty<div class="panel form-panel"><h2>You're all caught up</h2><p class="muted">New pricing requests from Muscat will appear here.</p></div>@endforelse</div>
@endsection
