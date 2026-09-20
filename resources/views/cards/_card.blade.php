@php
    $accent = match ($student->class?->name) {
        '4A' => 'var(--teal)',
        '4B' => 'var(--pink)',
        default => 'var(--teal)',
    };
    $initials = collect(preg_split('/\s+/', trim($student->name)))
        ->filter()
        ->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->join('');
@endphp
<div class="bc-card" style="--accent:{{ $accent }}">
    <div class="bc-head">
        <div class="bc-brand">
            <span class="material-symbols-outlined fill">school</span>
            <div>
                <div class="bc-school">SD Inpres Tamamaung IV</div>
                <div class="bc-sub">Kartu Absensi Siswa</div>
            </div>
        </div>
        <span class="bc-chip">{{ $student->class->name }}</span>
    </div>
    <div class="bc-body">
        <div class="bc-avatar">{{ $initials }}</div>
        <div class="bc-info">
            <div class="bc-name">{{ $student->name }}</div>
            <div class="bc-meta">NIS {{ $student->nis }} · NISN {{ $student->nisn }}</div>
            <div class="bc-meta">Kelas {{ $student->class->name }} · {{ $student->gender }}</div>
        </div>
    </div>
    <div class="bc-qr">
        <div class="bc-qr-label">Scan untuk absensi</div>
        <div class="bc-qr-box">{!! QrCode::size(150)->generate($student->barcode_value) !!}</div>
    </div>
    <div class="bc-code"><code>{{ $student->barcode_value }}</code></div>
    <div class="bc-foot">Bawa kartu ini setiap hari saat absen</div>
</div>