<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}"><title>{{ $title ?? 'Lustrous Glory International Quotes' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@auth
<header class="topbar">
    <a class="brand" href="{{ auth()->user()->role === 'pricing' ? route('pricing.index') : route('dashboard') }}">
        <span class="brand-mark">LGI</span><span>Lustrous Glory International <small>quotation workspace</small></span>
    </a>
    <nav>
        @if(auth()->user()->role !== 'pricing')<a href="{{ route('dashboard') }}">Overview</a><a href="{{ route('quotations.index') }}">Quotations</a>@endif
        @if(in_array(auth()->user()->role, ['pricing','admin']))<a href="{{ route('pricing.index') }}">Price desk</a>@endif
        @if(auth()->user()->role === 'admin')<a href="{{ route('products.index') }}">Products</a><a href="{{ route('users.index') }}">Users</a>@endif
    </nav>
    <div class="profile"><span>{{ auth()->user()->name }}<small>{{ auth()->user()->office }}</small></span>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="icon-btn" aria-label="Sign out">↗</button></form></div>
</header>
@endauth
<main class="shell {{ auth()->check() && auth()->user()->role === 'pricing' ? 'mobile-shell' : '' }}">
    @if(session('success'))<div class="flash">✓ {{ session('success') }}</div>@endif
    @if($errors->any())<div class="errors"><strong>Please check the form.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</main>
@stack('scripts')
</body></html>
