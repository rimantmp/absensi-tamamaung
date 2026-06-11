@extends('layouts.app', ['title' => 'Login', 'public' => true])
@section('actions')
    <a class="btn secondary" href="{{ route('scan.public') }}"><span class="material-symbols-outlined">qr_code_scanner</span>Scan Mandiri</a>
@endsection
@section('content')
<header class="public-head" style="margin-bottom:24px">
    <span class="material-symbols-outlined fill school-icon">school</span>
    <h1>Login</h1>
    <p class="subtitle">Masuk untuk area internal atau fitur pribadi siswa.</p>
</header>

<div class="result-card" style="width:100%;max-width:460px;padding:24px;background:var(--surface-lowest)">
    <form method="post" action="{{ route('login.post') }}" class="grid">
        @csrf
        <label>Username atau Email<input name="login" value="{{ old('login') }}" autofocus></label>
        @error('login')<div class="err">{{ $message }}</div>@enderror
        <label>Password<input name="password" type="password"></label>
        <label style="display:flex;align-items:center;gap:10px;font-weight:500">
            <input style="width:auto;min-height:auto" type="checkbox" name="remember"> Ingat saya
        </label>
        <button class="btn" style="width:100%"><span class="material-symbols-outlined">login</span>Login</button>
    </form>
</div>
@endsection
