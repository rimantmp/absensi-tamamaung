<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page{size:A4 landscape;margin:15mm 12mm 18mm 12mm}
    body{font-family:Inter,'Segoe UI',sans-serif;color:#1d1c16;font-size:11px;line-height:1.5}
    .kop{text-align:center;margin-bottom:2px}
    .kop .school{font-size:19px;font-weight:700;letter-spacing:1px;text-transform:uppercase}
    .kop .addr{font-size:11px;color:#555}
    hr{border:none;border-top:3px double #000;margin:8px 0 14px}
    h2.title{text-align:center;text-transform:uppercase;letter-spacing:.5px;font-size:15px;margin:0 0 4px;text-decoration:underline}
    h3{margin:18px 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:.4px}
    table{width:100%;border-collapse:collapse;margin-bottom:10px}
    th,td{border:1px solid #999;padding:4px 6px;text-align:left;font-size:10px}
    th{background:#f2ede4}
    .meta td{border:none;border-bottom:1px dotted #ccc;padding:2px 6px}
    .c{text-align:center}.r{text-align:right}
    .pb{page-break-before:always}
    .sign{display:flex;justify-content:space-between;margin-top:56px}
    .sign .blk{width:45%;text-align:center;font-size:11px}
    .sign .name{margin-top:52px}
</style>
</head>
<body>
<div class="kop">
    <div class="school">SD Inpres Tamamaung IV</div>
    <div class="addr">Kecamatan Tamamaung, Kota Makassar — Sulawesi Selatan</div>
</div>
<hr>
<h2 class="title">Laporan Absensi Siswa</h2>
<table class="meta">
    <tr><td style="width:130px">Periode</td><td><b>{{ $period }}</b></td><td style="width:130px">Kelas</td><td><b>{{ $class ?? 'Semua' }}</b></td></tr>
    <tr><td>Status</td><td><b>{{ $status ?? 'Semua' }}</b></td><td>Siswa</td><td><b>{{ $student ?? 'Semua' }}</b></td></tr>
    <tr><td>Dicetak oleh</td><td><b>{{ $user }}</b></td><td>Tanggal cetak</td><td><b>{{ now()->format('d/m/Y H:i') }}</b></td></tr>
</table>

<h3>A. Ringkasan Kehadiran</h3>
<table>
    <tr><th class="c">Hadir</th><th class="c">Terlambat</th><th class="c">Izin</th><th class="c">Sakit</th><th class="c">Alpa</th><th class="c">Total Data</th><th class="c">% Kehadiran</th></tr>
    <tr>
        <td class="c">{{ $summary['Hadir'] }}</td>
        <td class="c">{{ $summary['Terlambat'] }}</td>
        <td class="c">{{ $summary['Izin'] }}</td>
        <td class="c">{{ $summary['Sakit'] }}</td>
        <td class="c">{{ $summary['Alpa'] }}</td>
        <td class="c"><b>{{ $summary['total'] }}</b></td>
        <td class="c"><b>{{ $summary['rate'] }}%</b></td>
    </tr>
</table>

<h3>B. Rekap per Kelas</h3>
<table>
    <tr><th class="c">No</th><th>Kelas</th><th class="c">Hadir</th><th class="c">Terlambat</th><th class="c">Izin</th><th class="c">Sakit</th><th class="c">Alpa</th><th class="c">Total</th><th class="c">% Kehadiran</th></tr>
    @foreach($recapByClass as $i => $r)
    <tr>
        <td class="c">{{ $loop->iteration }}</td>
        <td>{{ $r['name'] }}</td>
        <td class="c">{{ $r['Hadir'] }}</td>
        <td class="c">{{ $r['Terlambat'] }}</td>
        <td class="c">{{ $r['Izin'] }}</td>
        <td class="c">{{ $r['Sakit'] }}</td>
        <td class="c">{{ $r['Alpa'] }}</td>
        <td class="c"><b>{{ $r['total'] }}</b></td>
        <td class="c">{{ $r['rate'] }}%</td>
    </tr>
    @endforeach
</table>

@if($recapByStudent->isNotEmpty())
<div class="pb"></div>
<h3>C. Rekap per Siswa</h3>
<table>
    <tr><th class="c">No Absen</th><th>NIS</th><th>Nama Siswa</th><th>Kelas</th><th class="c">Hadir</th><th class="c">Terlambat</th><th class="c">Izin</th><th class="c">Sakit</th><th class="c">Alpa</th><th class="c">Total</th><th class="c">%</th></tr>
    @foreach($recapByStudent as $r)
    <tr>
        <td class="c">{{ $r['attendance_number'] }}</td>
        <td>{{ $r['nis'] }}</td>
        <td>{{ $r['name'] }}</td>
        <td>{{ $r['class'] }}</td>
        <td class="c">{{ $r['Hadir'] }}</td>
        <td class="c">{{ $r['Terlambat'] }}</td>
        <td class="c">{{ $r['Izin'] }}</td>
        <td class="c">{{ $r['Sakit'] }}</td>
        <td class="c">{{ $r['Alpa'] }}</td>
        <td class="c"><b>{{ $r['total'] }}</b></td>
        <td class="c">{{ $r['rate'] }}%</td>
    </tr>
    @endforeach
</table>
@endif

<div class="pb"></div>
<h3>D. Detail Absensi</h3>
@include('reports._detail')

<div class="sign">
    <div class="blk">
        Makassar, {{ now()->format('d F Y') }}<br>
        Petugas,
        <div class="name">( {{ $user }} )</div>
    </div>
    <div class="blk">
        Mengetahui,<br>
        Kepala SD Inpres Tamamaung IV
        <div class="name">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
    </div>
</div>
@if($autoPrint ?? false)
<style>
    .print-bar{position:fixed;top:12px;right:12px;display:flex;gap:8px;z-index:9}
    .print-bar button{min-height:36px;padding:8px 14px;border-radius:8px;border:1px solid #999;background:#fff;color:#1d1c16;font:600 13px/1 Inter,'Segoe UI',sans-serif;cursor:pointer}
    @media print{.print-bar{display:none!important}}
</style>
<div class="print-bar">
    <button type="button" onclick="window.print()">Cetak Ulang</button>
    <button type="button" onclick="window.close()">Tutup</button>
</div>
<script>window.addEventListener('load', () => setTimeout(() => window.print(), 400));</script>
@endif
</body>
</html>
