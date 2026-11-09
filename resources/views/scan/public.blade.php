@extends('layouts.app', ['title' => 'Absensi Mandiri Siswa', 'public' => true])
@section('actions')<a class="btn secondary" href="{{ route('login') }}"><span class="material-symbols-outlined">login</span>Login</a>@endsection
@section('content')
<header class="public-head">
    <span class="material-symbols-outlined fill school-icon">school</span>
    <h1>Selamat Datang di SD Inpres Tamamaung IV</h1>
    <p class="subtitle">Silakan arahkan kode QR atau barcode kartu pelajar Anda ke scanner / kamera di bawah ini.</p>

    <div class="public-live-bar" style="display:inline-flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:12px;margin:20px auto 0;padding:12px 22px;background:#fff;border:1px solid var(--hairline);border-radius:14px;box-shadow:0 2px 10px rgba(16,24,40,.04);max-width:680px">
        <div style="display:flex;align-items:center;gap:8px;font-weight:600;color:var(--navy);font-size:14px">
            <span class="material-symbols-outlined" style="color:var(--primary);font-size:20px">calendar_month</span>
            <span id="public-live-day">{{ now()->locale('id')->translatedFormat('l') }}</span>,
            <span id="public-live-date">{{ now()->locale('id')->translatedFormat('d F Y') }}</span>
        </div>
        <span style="color:var(--outline-variant)">•</span>
        <div style="display:flex;align-items:center;gap:8px;font-weight:700;color:var(--ink);font-size:14px">
            <span class="material-symbols-outlined" style="color:var(--primary);font-size:20px">schedule</span>
            <span id="public-live-clock" style="font-family:ui-monospace,SFMono-Regular,Consolas,monospace;font-size:16px;letter-spacing:.5px">{{ now()->format('H:i:s') }} WITA</span>
        </div>
        @if(isset($setting) && $setting->late_time_limit)
        <span style="color:var(--outline-variant)">•</span>
        <div style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;padding:4px 10px;border-radius:999px;background:#fff3dc;color:#9a5b13">
            <span class="material-symbols-outlined" style="font-size:15px">timer</span>
            Batas Masuk: {{ substr($setting->late_time_limit, 0, 5) }} WITA
        </div>
        @endif
    </div>
</header>
@include('scan.partials', ['public' => true])
@endsection

@push('scripts')
<script>
(() => {
    const dayEl = document.getElementById('public-live-day');
    const dateEl = document.getElementById('public-live-date');
    const clockEl = document.getElementById('public-live-clock');
    if (!clockEl) return;

    const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    const tick = () => {
        const d = new Date();
        if (dayEl) dayEl.textContent = days[d.getDay()];
        if (dateEl) dateEl.textContent = `${String(d.getDate()).padStart(2, '0')} ${months[d.getMonth()]} ${d.getFullYear()}`;
        clockEl.textContent = `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}:${String(d.getSeconds()).padStart(2, '0')} WITA`;
    };

    tick();
    setInterval(tick, 1000);
})();
</script>
@endpush
