<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Daftar Nilai - {{ $class->name }}</title>
<style>
    @page{size:A4 landscape;margin:15mm 12mm 18mm 12mm}
    body{font-family:Inter,'Segoe UI',sans-serif;color:#1d1c16;font-size:11px;line-height:1.5}
    .kop{text-align:center;margin-bottom:2px}
    .kop .school{font-size:19px;font-weight:700;letter-spacing:1px;text-transform:uppercase}
    .kop .addr{font-size:11px;color:#555}
    hr{border:none;border-top:3px double #000;margin:8px 0 14px}
    h2.title{text-align:center;text-transform:uppercase;letter-spacing:.5px;font-size:15px;margin:0 0 4px;text-decoration:underline}
    table{width:100%;border-collapse:collapse;margin-bottom:10px}
    th,td{border:1px solid #999;padding:4px 6px;font-size:10px}
    th{background:#f2ede4}
    td.c,th.c{text-align:center}
    .meta{margin-bottom:8px;font-size:11px}
    .sign{display:flex;justify-content:flex-end;margin-top:48px}
    .sign .blk{width:45%;text-align:center;font-size:11px}
    .sign .name{margin-top:52px}
    .print-bar{position:fixed;top:12px;right:12px;display:flex;gap:8px;z-index:9}
    .print-bar button{min-height:36px;padding:8px 14px;border-radius:8px;border:1px solid #999;background:#fff;color:#1d1c16;font:600 13px/1 Inter,'Segoe UI',sans-serif;cursor:pointer}
    @media print{.print-bar{display:none!important}}
</style>
</head>
<body>
<div class="kop">
    <div class="school">SD Inpres Tamamaung IV</div>
    <div class="addr">Kecamatan Tamamaung, Kota Makassar — Sulawesi Selatan</div>
</div>
<hr>
<h2 class="title">Daftar Nilai Siswa</h2>
<div class="meta">
    Kelas: <b>{{ $class->name }}</b> &nbsp;·&nbsp; Tahun Ajaran: <b>{{ $schoolYear }}</b> &nbsp;·&nbsp; Semester: <b>{{ $semester }}</b> &nbsp;·&nbsp; Wali Kelas: <b>{{ $class->homeroomTeacher->name ?? '-' }}</b>
</div>

<table>
    <tr>
        <th class="c">No</th><th>NIS</th><th>Nama Siswa</th>
        @foreach($subjects as $sub)<th class="c">{{ $sub->name }}</th>@endforeach
        <th class="c">Rata-rata</th>
    </tr>
    @foreach($students as $i => $student)
    <tr>
        <td class="c">{{ $i + 1 }}</td>
        <td>{{ $student->nis }}</td>
        <td>{{ $student->name }}</td>
        @php($finals = [])
        @foreach($subjects as $sub)
            @php($grade = $grades->get("{$student->id}:{$sub->id}"))
            @php($final = $grade?->finalScore())
            @if($final !== null)@php($finals[] = $final)@endif
            <td class="c">{{ $final !== null ? number_format($final, 2) : '-' }}</td>
        @endforeach
        <td class="c"><b>{{ $finals ? number_format(round(array_sum($finals) / count($finals), 2), 2) : '-' }}</b></td>
    </tr>
    @endforeach
    @if($students->isEmpty())
    <tr><td colspan="{{ $subjects->count() + 4 }}">Tidak ada siswa aktif.</td></tr>
    @endif
</table>

@if($autoPrint ?? false)
<div class="print-bar">
    <button type="button" onclick="window.print()">Cetak Ulang</button>
    <button type="button" onclick="window.close()">Tutup</button>
</div>
<script>window.addEventListener('load', () => setTimeout(() => window.print(), 400));</script>
@endif
</body>
</html>
