<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Absensi SD Inpres Tamamaung IV' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        :root{--background:#fef9ef;--surface:#fef9ef;--surface-lowest:#fff;--surface-low:#f8f3e9;--surface-container:#f2ede4;--surface-high:#ece8de;--surface-highest:#e7e2d8;--surface-dim:#dedad0;--ink:#0a0a0a;--on-surface:#1d1c16;--body:#3a3a3a;--muted:#6a6a6a;--outline:#747878;--outline-variant:#c4c7c7;--hairline:#e5e5e5;--primary:#000;--on-primary:#fff;--pink:#ff4d8b;--teal:#1a3a3a;--lav:#b8a4ed;--peach:#ffb084;--ochre:#e8b94a;--success:#22c55e;--error:#ef4444;--sidebar:260px}
        *{box-sizing:border-box}html{font-family:Inter,ui-sans-serif,system-ui,sans-serif}body{margin:0;background:var(--background);color:var(--on-surface);font-size:16px;line-height:1.55;-webkit-font-smoothing:antialiased}a{text-decoration:none;color:inherit}.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24}.fill{font-variation-settings:'FILL' 1,'wght' 500,'GRAD' 0,'opsz' 24}
        .shell{min-height:100vh;display:flex}.side{position:fixed;inset:0 auto 0 0;width:var(--sidebar);height:100vh;background:var(--surface-low);border-right:1px solid var(--hairline);padding:16px;display:flex;flex-direction:column;z-index:40}.main{flex:1;margin-left:var(--sidebar);padding:32px;width:calc(100% - var(--sidebar));min-width:0}.main-inner{width:100%;max-width:none;margin:0;display:flex;flex-direction:column;gap:24px}
        .brand-row{display:flex;gap:12px;align-items:center;margin:4px 4px 32px}.logo{width:40px;height:40px;border-radius:12px;background:var(--ochre);display:grid;place-items:center;color:var(--primary);flex:0 0 auto}.brand{font-size:18px;line-height:1.15;font-weight:600;color:var(--ink)}.muted{color:var(--muted);font-size:14px}.tiny{font-size:13px}.quick{width:100%;margin-bottom:24px}
        .nav{display:flex;flex-direction:column;gap:4px;overflow-y:auto;flex:1}.nav a,.logout{display:flex;align-items:center;gap:12px;width:100%;padding:10px 12px;border-radius:8px;border:0;background:transparent;color:var(--muted);font:500 14px/1.4 Inter,sans-serif;text-align:left;cursor:pointer}.nav a:hover,.logout:hover{background:var(--surface-container);color:var(--ink)}.nav a.active{background:var(--surface-high);color:var(--ink);font-weight:600}.nav-footer{border-top:1px solid var(--hairline);padding-top:16px;margin-top:16px;display:flex;flex-direction:column;gap:4px}
        .mobile-bar{display:none}.page-head{display:flex;justify-content:space-between;align-items:flex-end;gap:16px}.h1{font-size:clamp(30px,3vw,40px);line-height:1.1;letter-spacing:-1px;font-weight:500;color:var(--ink);margin:0}.subtitle{margin:4px 0 0;color:var(--muted);font-size:16px}.actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}.grid{display:grid;gap:16px}.stats{grid-template-columns:repeat(auto-fit,minmax(220px,1fr))}.two{grid-template-columns:repeat(auto-fit,minmax(min(100%,420px),1fr))}
        .card{background:var(--surface-lowest);border:1px solid var(--hairline);border-radius:12px;padding:24px;overflow-x:auto}.panel{background:var(--surface-low);border:1px solid var(--hairline);border-radius:12px;padding:24px;overflow-x:auto}.stat{min-height:150px;border-radius:12px;padding:24px;border:1px solid rgba(0,0,0,.08);display:flex;flex-direction:column;justify-content:space-between}.stat h2{font-size:40px;line-height:1.1;letter-spacing:-1px;font-weight:500;margin:8px 0 0}.stat .material-symbols-outlined{font-size:26px}.pink{background:var(--pink);color:white}.teal{background:var(--teal);color:white}.lav{background:var(--lav);color:var(--ink)}.peach{background:var(--peach);color:var(--ink)}.ochre{background:var(--ochre);color:var(--ink)}.surface{background:var(--surface-low)}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:40px;padding:10px 16px;border-radius:8px;border:1px solid var(--primary);background:var(--primary);color:var(--on-primary);font:600 14px/1 Inter,sans-serif;cursor:pointer}.btn.secondary{background:transparent;color:var(--primary);border-color:var(--hairline)}.btn.icon{width:40px;padding:0;border-radius:999px}
        input,select,textarea{width:100%;min-height:44px;border:1px solid var(--hairline);border-radius:8px;background:var(--surface);padding:10px 12px;color:var(--ink);font:400 14px/1.4 Inter,sans-serif}input:focus,select:focus,textarea:focus{outline:2px solid rgba(0,0,0,.12);border-color:var(--primary)}label{display:grid;gap:6px;font-size:13px;font-weight:600;color:var(--body)}table{width:100%;min-width:560px;border-collapse:collapse;background:transparent}th,td{text-align:left;border-bottom:1px solid var(--hairline);padding:14px 12px;font-size:14px;vertical-align:middle}th{font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted);font-weight:600}tr:last-child td{border-bottom:0}.alert{padding:12px 14px;border-radius:8px;background:#dcfce7;border:1px solid rgba(34,197,94,.25);margin:0}.err{color:#b91c1c;font-size:13px}
        .scan-box{min-height:320px;border:1px solid var(--hairline);border-radius:12px;background:var(--surface-high);display:grid;place-items:center;overflow:hidden;position:relative}.scan-box:before{content:"";position:absolute;inset:24px;border:4px solid var(--ochre);border-radius:12px;clip-path:polygon(0 0,22% 0,22% 8px,8px 8px,8px 22%,0 22%,0 0,78% 0,100% 0,100% 22%,calc(100% - 8px) 22%,calc(100% - 8px) 8px,78% 8px,78% 0,100% 78%,100% 100%,78% 100%,78% calc(100% - 8px),calc(100% - 8px) calc(100% - 8px),calc(100% - 8px) 78%,100% 78%,22% 100%,0 100%,0 78%,8px 78%,8px calc(100% - 8px),22% calc(100% - 8px),22% 100%)}.scan-box:after{content:"";position:absolute;left:32px;right:32px;height:2px;background:var(--success);box-shadow:0 0 12px var(--success);animation:scan 2.4s linear infinite}@keyframes scan{0%{top:32px;opacity:0}10%,90%{opacity:1}100%{top:calc(100% - 32px);opacity:0}}
        .result-card{background:var(--surface-lowest);border:1px solid var(--hairline);border-radius:12px;padding:20px}.status-badge{display:inline-flex;align-items:center;gap:6px;border-radius:999px;border:1px solid var(--hairline);padding:6px 10px;font-size:13px;font-weight:600}.print-card{width:320px;border:1px solid var(--hairline);border-radius:8px;background:var(--surface-lowest);padding:18px;break-inside:avoid}.cards-grid{display:flex;flex-wrap:wrap;gap:16px}
        .public-shell{min-height:100vh;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:64px 24px;background:var(--background)}.public-head{text-align:center;max-width:720px;margin-bottom:32px}.public-head .school-icon{font-size:48px;color:var(--ochre);margin-bottom:16px}.public-head h1{font-size:40px;line-height:1.1;letter-spacing:-1px;font-weight:500;margin:0 0 8px;color:var(--ink)}.public-main{width:100%;max-width:720px;display:flex;flex-direction:column;gap:24px}.public-login{position:fixed;top:20px;right:20px}
        @media(max-width:1180px){:root{--sidebar:232px}.main{padding:24px}.stats{grid-template-columns:repeat(auto-fit,minmax(200px,1fr))}.side{padding:14px}.brand{font-size:16px}.nav a,.logout{padding:10px}}
        @media(max-width:900px){.shell{display:block}.side{position:static;width:100%;height:auto;border-right:0;border-bottom:1px solid var(--hairline);padding:12px}.main{margin-left:0;width:100%;padding:16px}.main-inner{gap:16px}.mobile-bar{display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:var(--surface-low);border-bottom:1px solid var(--hairline)}.stats,.two{grid-template-columns:1fr}.page-head{align-items:flex-start;flex-direction:column}.h1,.public-head h1{font-size:32px;letter-spacing:-.5px}.side .brand-row{margin:0 0 12px}.quick{margin-bottom:12px}.nav{display:flex;flex-direction:row;gap:8px;overflow-x:auto;max-height:none;padding-bottom:4px}.nav a,.logout{white-space:nowrap;flex:0 0 auto}.nav-footer{flex-direction:row;align-items:center;overflow-x:auto;margin-top:10px;padding-top:10px}.nav-footer .muted{display:none}.public-shell{justify-content:flex-start;padding:32px 16px}.public-login{position:static;margin-bottom:24px;align-self:flex-end}}
        @media(max-width:560px){.main{padding:12px}.card,.panel,.result-card{padding:16px}.stat{min-height:128px;padding:18px}.stat h2{font-size:34px}.actions,.btn{width:100%}.actions .btn{width:100%}input,select,textarea{font-size:16px}}
        @media print{.side,.mobile-bar,.page-head .actions,.no-print,.btn{display:none!important}.shell{display:block}.main{margin:0;padding:0;width:100%;max-width:none}.print-card{box-shadow:none}}
    </style>
    @livewireStyles
</head>
<body>
@php($isPublic = $public ?? false)
@if($isPublic)
    <div class="public-shell">
        <div class="public-login">@yield('actions')</div>
        @yield('content')
    </div>
@else
<div class="shell">
    <aside class="side">
        <div class="brand-row">
            <div class="logo"><span class="material-symbols-outlined fill">school</span></div>
            <div>
                <div class="brand">SD Inpres Tamamaung IV</div>
                <div class="muted tiny">Attendance System</div>
            </div>
        </div>
        @auth
            @if(auth()->user()->hasAnyRole(['Admin','Guru']))
                <a class="btn quick" href="{{ route('scan.internal') }}"><span class="material-symbols-outlined">qr_code_scanner</span>Quick Scan</a>
            @endif
        @endauth
        <nav class="nav">
            @auth
                @unless(auth()->user()->hasRole('Siswa'))
                    <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? 'fill' : '' }}">dashboard</span>Dashboard</a>
                    @if(auth()->user()->hasRole('Admin'))
                        <a class="{{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('students.*') ? 'fill' : '' }}">groups</span>Data Siswa</a>
                        <a class="{{ request()->routeIs('classes.*') ? 'active' : '' }}" href="{{ route('classes.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('classes.*') ? 'fill' : '' }}">class</span>Data Kelas</a>
                        <a class="{{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('users.*') ? 'fill' : '' }}">badge</span>Data Pengguna</a>
                        <a class="{{ request()->routeIs('cards.index') ? 'active' : '' }}" href="{{ route('cards.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('cards.index') ? 'fill' : '' }}">qr_code_2</span>Barcode/QR Siswa</a>
                    @endif
                    @if(auth()->user()->hasAnyRole(['Admin','Guru']))
                        <a class="{{ request()->routeIs('scan.internal') ? 'active' : '' }}" href="{{ route('scan.internal') }}"><span class="material-symbols-outlined {{ request()->routeIs('scan.internal') ? 'fill' : '' }}">qr_code_scanner</span>Scan Internal</a>
                        <a class="{{ request()->routeIs('attendance.manual') ? 'active' : '' }}" href="{{ route('attendance.manual') }}"><span class="material-symbols-outlined {{ request()->routeIs('attendance.manual') ? 'fill' : '' }}">edit_calendar</span>Absensi Manual</a>
                    @endif
                    <a class="{{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('reports.*') ? 'fill' : '' }}">assessment</span>Laporan</a>
                    @if(auth()->user()->hasRole('Admin'))
                        <a class="{{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('settings.*') ? 'fill' : '' }}">settings</span>Pengaturan Jam</a>
                    @endif
                @else
                    <a class="{{ request()->routeIs('student.history') ? 'active' : '' }}" href="{{ route('student.history') }}"><span class="material-symbols-outlined {{ request()->routeIs('student.history') ? 'fill' : '' }}">history</span>Riwayat Saya</a>
                    <a class="{{ request()->routeIs('cards.mine') ? 'active' : '' }}" href="{{ route('cards.mine') }}"><span class="material-symbols-outlined {{ request()->routeIs('cards.mine') ? 'fill' : '' }}">qr_code_2</span>Kartu Barcode Saya</a>
                @endunless
            @else
                <a href="{{ route('login') }}"><span class="material-symbols-outlined">login</span>Login</a>
            @endauth
        </nav>
        <div class="nav-footer">
            <a href="{{ route('scan.public') }}"><span class="material-symbols-outlined">qr_code_scanner</span>Scan Mandiri</a>
            @auth
                <div class="muted tiny" style="padding:8px 12px">{{ auth()->user()->name }}<br>{{ auth()->user()->roles->pluck('name')->first() }}</div>
                <form method="post" action="{{ route('logout') }}">@csrf<button class="logout"><span class="material-symbols-outlined">logout</span>Logout</button></form>
            @endauth
        </div>
    </aside>
    <main class="main">
        <div class="main-inner">
            <div class="page-head">
                <div>
                    <h1 class="h1">{{ $title ?? 'Dashboard' }}</h1>
                    <p class="subtitle">{{ $subtitle ?? 'Sistem absensi siswa berbasis QR/barcode.' }}</p>
                </div>
                <div class="actions">@yield('actions')</div>
            </div>
            @if(session('ok'))<div class="alert">{{ session('ok') }}</div>@endif
            @yield('content')
        </div>
    </main>
</div>
@endif
@livewireScripts
@stack('scripts')
</body>
</html>
