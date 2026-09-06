@extends('layout')
@section('content')
</main><div class="login-page">
    <section class="login-art"><div><div class="eyebrow" style="color:#70d8bc">Muscat ↔ Dubai</div><h1>Quotes move faster when pricing does.</h1><p>One secure workspace for your Muscat quotation team and Dubai price desk—built around Omani Rial and ready for both brands.</p></div>
        <div class="logo-pair"><div><img src="{{ asset('images/mais-logo.png') }}" alt="Mais"></div><div><img src="{{ asset('images/mudgi-logo.png') }}" alt="Mudgi"></div></div></section>
    <section class="login-form"><form class="login-box" method="post" action="{{ url('/login') }}">@csrf
        <div class="brand"><span class="brand-mark">LGI</span><span>Lustrous Glory International <small>quotation workspace</small></span></div>
        <h2>Welcome back</h2><p>Sign in with your work account.</p>
        <label class="field"><span>Email address</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
        <label class="field"><span>Password</span><input type="password" name="password" autocomplete="current-password" required></label>
        <label style="display:flex;gap:8px;margin-bottom:20px"><input type="checkbox" name="remember"> Keep me signed in</label>
        <button class="btn green full">Sign in securely</button>
    </form></section>
</div><main>
@endsection
