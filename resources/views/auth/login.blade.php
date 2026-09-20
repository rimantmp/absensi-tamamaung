@extends('layouts.app', ['title' => 'Masuk ke aplikasi', 'public' => true])

@section('content')
<div class="login-page">
    <div class="login-frame">
        <section class="login-story">
            <div class="login-copy">
                <div class="school-mark"><span class="material-symbols-outlined fill">school</span></div>
                <div class="login-eyebrow">SD INPRES TAMAMAUNG IV</div>
                <h1>Sistem Kehadiran Siswa</h1>
                <p>Absensi berbasis barcode yang cepat, akurat, dan mudah digunakan untuk sekolah modern.</p>
                <div class="login-benefits">
                    <div class="login-benefit"><span class="material-symbols-outlined">qr_code_scanner</span><b>Scan Cepat</b><small>Absensi hanya dalam 1 detik</small></div>
                    <div class="login-benefit"><span class="material-symbols-outlined fill">verified_user</span><b>Akurat &amp; Aman</b><small>Data tersimpan dengan aman</small></div>
                    <div class="login-benefit"><span class="material-symbols-outlined fill">bar_chart</span><b>Laporan Lengkap</b><small>Rekap otomatis dan mudah dipantau</small></div>
                </div>
            </div>
            <img class="login-illustration" src="{{ asset('images/login-attendance-hero.png') }}" alt="Siswa melakukan absensi menggunakan kartu barcode">
        </section>
        <section class="login-panel">
            <div class="login-box">
                <div class="login-lock"><span class="material-symbols-outlined fill">lock</span></div>
                <h2>Masuk ke aplikasi</h2>
                <p class="login-intro">Gunakan username atau email beserta password<br>untuk melanjutkan.</p>
                <form method="post" action="{{ route('login.post') }}" class="grid" style="gap:16px">
                    @csrf
                    <label>Username atau Email<div class="field-wrap"><span class="material-symbols-outlined">person</span><input name="login" value="{{ old('login') }}" placeholder="Masukkan username atau email" autocomplete="username" autofocus required></div></label>
                    @error('login')<div class="err">{{ $message }}</div>@enderror
                    <label>Password<div class="field-wrap"><span class="material-symbols-outlined">lock</span><input id="login-password" name="password" type="password" placeholder="Masukkan password" autocomplete="current-password" required><button class="password-toggle" type="button" aria-label="Tampilkan password" onclick="const input=document.getElementById('login-password'); input.type=input.type==='password'?'text':'password'; this.querySelector('span').textContent=input.type==='password'?'visibility':'visibility_off'"><span class="material-symbols-outlined">visibility</span></button></div></label>
                    <div class="login-options"><label class="remember"><input type="checkbox" name="remember"> Ingat saya</label><a class="login-link" href="{{ route('scan.public') }}">Scan mandiri</a></div>
                    <button class="btn login-submit"><span>Masuk</span><span class="material-symbols-outlined">arrow_forward</span></button>
                </form>
                <div class="login-footer"><span class="material-symbols-outlined" style="font-size:13px;vertical-align:-2px">shield</span> Sistem Kehadiran Siswa &copy; {{ date('Y') }}<br>SD Inpres Tamamaung IV</div>
            </div>
        </section>
    </div>
</div>
@endsection
