@extends('layouts.app', ['title' => 'Absensi Mandiri Siswa', 'public' => true])
@section('actions')<a class="btn secondary" href="{{ route('login') }}"><span class="material-symbols-outlined">login</span>Login</a>@endsection
@section('content')
<header class="public-head">
    <span class="material-symbols-outlined fill school-icon">school</span>
    <h1>Selamat Datang di SD Inpres Tamamaung IV</h1>
    <p class="subtitle">Silakan arahkan kode QR kartu pelajar Anda ke kamera di bawah ini.</p>
</header>
@include('scan.partials', ['public' => true])
@endsection
