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
        .result-card{background:var(--surface-lowest);border:1px solid var(--hairline);border-radius:12px;padding:20px}.status-badge{display:inline-flex;align-items:center;gap:6px;border-radius:999px;border:1px solid var(--hairline);padding:6px 10px;font-size:13px;font-weight:600}
        .pagination{display:flex;flex-direction:column;gap:12px;align-items:center;padding:20px 0 4px}.pagination-info{color:var(--muted);font-size:13px}.pagination-links{display:flex;flex-wrap:wrap;gap:8px;align-items:center;justify-content:center}.pagination-btn{display:inline-flex;align-items:center;justify-content:center;min-width:40px;min-height:40px;padding:8px 14px;border-radius:8px;border:1px solid var(--hairline);background:var(--surface-lowest);color:var(--ink);font:600 14px/1 Inter,sans-serif;cursor:pointer;user-select:none}.pagination-btn:hover:not(.disabled):not(.active){border-color:var(--outline);background:var(--surface-low)}.pagination-btn.active{background:var(--primary);border-color:var(--primary);color:var(--on-primary);cursor:default}.pagination-btn.disabled{opacity:.45;cursor:not-allowed}.pagination-gap{color:var(--muted);padding:0 4px}.print-card{width:320px;border:1px solid var(--hairline);border-radius:8px;background:var(--surface-lowest);padding:18px;break-inside:avoid}.cards-grid{display:flex;flex-wrap:wrap;gap:16px}
        .bc-card{width:320px;border-radius:18px;background:var(--surface-lowest);overflow:hidden;break-inside:avoid;box-shadow:0 1px 2px rgba(0,0,0,.05),0 10px 28px rgba(0,0,0,.08);-webkit-print-color-adjust:exact;print-color-adjust:exact}
        .bc-head{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:16px 18px;background:var(--accent);color:#fff}
        .bc-brand{display:flex;align-items:center;gap:12px;min-width:0}.bc-brand .material-symbols-outlined{font-size:34px}
        .bc-school{font-weight:800;font-size:15px;line-height:1.15;letter-spacing:.2px}.bc-sub{font-size:10px;opacity:.85;text-transform:uppercase;letter-spacing:.14em;margin-top:2px}
        .bc-chip{border:1px solid rgba(255,255,255,.55);border-radius:999px;padding:6px 12px;font-size:13px;font-weight:800;letter-spacing:.5px;background:rgba(255,255,255,.16);flex:0 0 auto}
        .bc-body{display:flex;align-items:center;gap:14px;padding:16px 18px}
        .bc-avatar{width:54px;height:54px;border-radius:14px;background:color-mix(in srgb,var(--accent) 15%,#fff);color:var(--accent);display:grid;place-items:center;font-weight:800;font-size:20px;flex:0 0 auto}
        .bc-name{font-size:16px;font-weight:800;color:var(--ink);letter-spacing:.2px}.bc-meta{font-size:12px;color:var(--muted);margin-top:2px}
        .bc-qr{display:grid;place-items:center;gap:8px;padding:0 18px 14px}.bc-qr-label{font-size:10px;font-weight:700;letter-spacing:.18em;color:var(--outline);text-transform:uppercase}
        .bc-qr-box{background:#fff;border:2px dashed var(--outline-variant);border-radius:14px;padding:10px;line-height:0}
        .bc-code{text-align:center;padding:0 18px 16px}.bc-code code{font-family:ui-monospace,'Cascadia Mono',Consolas,monospace;font-size:13px;font-weight:600;letter-spacing:1px;color:var(--accent);background:color-mix(in srgb,var(--accent) 8%,#fff);padding:7px 14px;border-radius:8px}
        .bc-foot{background:var(--surface-low);border-top:1px solid var(--hairline);padding:10px 18px;font-size:11px;color:var(--muted);text-align:center}
        .public-shell{min-height:100vh;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:64px 24px;background:var(--background)}.public-head{text-align:center;max-width:720px;margin-bottom:32px}.public-head .school-icon{font-size:48px;color:var(--ochre);margin-bottom:16px}.public-head h1{font-size:40px;line-height:1.1;letter-spacing:-1px;font-weight:500;margin:0 0 8px;color:var(--ink)}.public-main{width:100%;max-width:720px;display:flex;flex-direction:column;gap:24px}.public-login{position:fixed;top:20px;right:20px}
        @media(max-width:1180px){:root{--sidebar:232px}.main{padding:24px}.stats{grid-template-columns:repeat(auto-fit,minmax(200px,1fr))}.side{padding:14px}.brand{font-size:16px}.nav a,.logout{padding:10px}}
        @media(max-width:900px){.shell{display:block}.side{position:static;width:100%;height:auto;border-right:0;border-bottom:1px solid var(--hairline);padding:12px}.main{margin-left:0;width:100%;padding:16px}.main-inner{gap:16px}.mobile-bar{display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:var(--surface-low);border-bottom:1px solid var(--hairline)}.stats,.two{grid-template-columns:1fr}.page-head{align-items:flex-start;flex-direction:column}.h1,.public-head h1{font-size:32px;letter-spacing:-.5px}.side .brand-row{margin:0 0 12px}.quick{margin-bottom:12px}.nav{display:flex;flex-direction:row;gap:8px;overflow-x:auto;max-height:none;padding-bottom:4px}.nav a,.logout{white-space:nowrap;flex:0 0 auto}.nav-footer{flex-direction:row;align-items:center;overflow-x:auto;margin-top:10px;padding-top:10px}.nav-footer .muted{display:none}.public-shell{justify-content:flex-start;padding:32px 16px}.public-login{position:static;margin-bottom:24px;align-self:flex-end}}
        @media(max-width:560px){.main{padding:12px}.card,.panel,.result-card{padding:16px}.stat{min-height:128px;padding:18px}.stat h2{font-size:34px}.actions,.btn{width:100%}.actions .btn{width:100%}input,select,textarea{font-size:16px}}
        @media print{.side,.mobile-bar,.page-head .actions,.no-print,.btn,.topbar{display:none!important}.shell{display:block}.main{margin:0;padding:0;width:100%;max-width:none}.print-card,.bc-card{box-shadow:none;border:1px solid #ddd}}

        /* Tamamaung 2026 visual system */
        :root{--background:#f4f7fb;--surface:#f8fafc;--surface-lowest:#fff;--surface-low:#f1f5f9;--surface-container:#e9eff7;--surface-high:#e3ebf5;--surface-highest:#dbe5f1;--ink:#101828;--on-surface:#1d2939;--body:#344054;--muted:#667085;--outline:#98a2b3;--outline-variant:#d0d5dd;--hairline:#e4e7ec;--primary:#1769e0;--on-primary:#fff;--pink:#fee4e2;--teal:#dcfae6;--lav:#e9e7fd;--peach:#fef0c7;--ochre:#eaf2ff;--success:#12b76a;--error:#f04438;--navy:#073765;--navy-2:#0a457d;--sidebar:268px}
        body{background:var(--background);font-size:14px}.shell{background:var(--background)}
        .side{background:linear-gradient(180deg,#073765 0%,#062f58 100%);border:0;padding:18px 12px;color:#fff;box-shadow:6px 0 24px rgba(7,55,101,.08)}
        .brand-row{margin:0 4px 24px;padding:0 2px}.logo{width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.1);color:#8ec5ff;border:1px solid rgba(255,255,255,.12)}.brand{font-size:14px;color:#fff}.brand-row .muted{color:#a9c6df;font-size:10px;text-transform:uppercase;letter-spacing:.08em}.quick{background:#fff;color:var(--navy);border-color:#fff;box-shadow:none}
        .nav{gap:2px}.nav-section{padding:15px 12px 5px;color:#739aba;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.1em}.nav a,.logout{color:#c4d8e9;border-radius:8px;padding:9px 11px;font-size:13px}.nav a .material-symbols-outlined,.logout .material-symbols-outlined{font-size:19px;color:#8fc2ed}.nav a:hover,.logout:hover{background:rgba(255,255,255,.08);color:#fff}.nav a.active{background:rgba(255,255,255,.14);color:#fff;font-weight:600;box-shadow:inset 2px 0 #70b7ff}.nav a.active .material-symbols-outlined{color:#fff}.nav-footer{border-color:rgba(255,255,255,.12);margin-top:12px;padding-top:12px}.nav-footer .muted{color:#a9c6df}
        .main{padding:0 28px 32px}.topbar{height:68px;margin:0 -28px 28px;padding:0 28px;background:#fff;border-bottom:1px solid var(--hairline);display:flex;align-items:center;justify-content:space-between;gap:20px}.topbar-title{font-size:15px;font-weight:700;color:var(--ink)}.topbar-crumb{font-size:11px;color:var(--muted);margin-top:1px}.topbar-profile{position:relative;border-left:1px solid var(--hairline);padding-left:18px}.topbar-user{display:flex;align-items:center;gap:10px;border:0;background:transparent;padding:3px 0;min-width:176px;text-align:left;color:var(--on-surface);font:inherit;cursor:pointer;border-radius:8px}.topbar-user:hover{background:#f8fafc}.topbar-user>.material-symbols-outlined{margin-left:auto;font-size:19px;color:var(--muted);transition:transform .2s}.topbar-profile.open .topbar-user>.material-symbols-outlined{transform:rotate(180deg)}.topbar-avatar{width:34px;height:34px;border-radius:50%;background:#e5efff;color:var(--navy);display:grid;place-items:center;font-weight:700;flex:0 0 auto}.topbar-name{font-weight:600;line-height:1.2}.topbar-role{font-size:10px;color:var(--muted);text-transform:uppercase}.topbar-clock{margin-left:auto;color:var(--body);font-size:12px;display:flex;align-items:center;gap:6px}.topbar-clock .material-symbols-outlined{font-size:17px;color:var(--primary)}.user-dropdown{display:none;position:absolute;z-index:80;right:0;top:calc(100% + 12px);width:220px;padding:8px;background:#fff;border:1px solid var(--hairline);border-radius:12px;box-shadow:0 14px 35px rgba(16,24,40,.14)}.topbar-profile.open .user-dropdown{display:block}.dropdown-identity{padding:9px 10px 11px;border-bottom:1px solid var(--hairline);margin-bottom:5px}.dropdown-identity strong{display:block;font-size:13px}.dropdown-identity span{display:block;color:var(--muted);font-size:11px;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.dropdown-logout{display:flex;align-items:center;gap:9px;width:100%;padding:10px;border:0;border-radius:8px;background:transparent;color:#d92d20;font:600 13px Inter,sans-serif;cursor:pointer;text-align:left}.dropdown-logout:hover{background:#fff1f0}.dropdown-logout .material-symbols-outlined{font-size:19px}
        .main-inner{gap:18px;max-width:1440px;margin:0 auto}.page-head{align-items:center}.h1{font-size:30px;font-weight:650;letter-spacing:-.7px}.subtitle{font-size:13px;margin-top:3px}.card,.panel,.result-card{border-color:var(--hairline);border-radius:14px;box-shadow:0 1px 2px rgba(16,24,40,.03);padding:20px}.stat{min-height:132px;border-radius:14px;border-color:var(--hairline);box-shadow:0 1px 3px rgba(16,24,40,.05)}.stat h2{font-size:34px;font-weight:650}.stat.ochre{background:#eaf2ff;color:#174a84}.stat.teal{background:#e7f8ef;color:#146c43}.stat.peach{background:#fff3dc;color:#9a5b13}.stat.lav{background:#f0efff;color:#5946a8}.stat .muted{color:inherit;opacity:.8}
        .btn{border:0;background:var(--primary);border-radius:9px;min-height:40px;padding:10px 15px;box-shadow:0 1px 2px rgba(16,24,40,.08)}.btn:hover{filter:brightness(.96)}.btn.secondary{background:#fff;color:var(--body);border:1px solid var(--outline-variant)}input,select,textarea{background:#fff;border-color:var(--outline-variant);border-radius:9px}input:focus,select:focus,textarea:focus{outline:3px solid rgba(23,105,224,.12);border-color:var(--primary)}th{background:#f8fafc;color:#667085}th,td{padding:12px}
        .bc-card{width:min(100%,330px);border-radius:15px;border:1px solid var(--hairline);box-shadow:0 4px 12px rgba(16,24,40,.06)}.bc-head{background:var(--navy);justify-content:center;text-align:center;padding:14px}.bc-brand .material-symbols-outlined,.bc-chip{display:none}.bc-school{font-size:10px;letter-spacing:.12em}.bc-sub{font-size:12px;font-weight:700;letter-spacing:0;text-transform:none;margin-top:4px}.bc-body{flex-direction:column;text-align:center;margin-top:-1px;padding:0 16px 10px}.bc-avatar{width:62px;height:62px;border-radius:50%;border:3px solid #fff;margin-top:-25px;background:#dceeff;color:var(--navy);box-shadow:0 2px 8px rgba(16,24,40,.12)}.bc-name{font-size:15px;margin-top:4px}.bc-meta{font-size:11px}.bc-qr{padding-bottom:10px}.bc-qr-label{display:none}.bc-qr-box{border:1px solid var(--hairline);border-radius:12px;padding:12px}.bc-code{padding-bottom:12px}.bc-code code{color:var(--navy);background:transparent;font-size:10px}.bc-foot{display:none}.cards-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px}.cards-grid .bc-card{width:100%}
        .public-shell:has(.login-page){padding:0;display:block;background:#f2f5fa}.public-shell:has(.login-page)>.public-login{display:none}.login-page{min-height:100vh;padding:22px;display:grid;place-items:center}.login-frame{width:min(1190px,100%);min-height:min(720px,calc(100vh - 44px));display:grid;grid-template-columns:1.03fr 1fr;background:#fff;border-radius:30px;overflow:hidden;box-shadow:0 24px 70px rgba(16,24,40,.12)}.login-story{position:relative;display:flex;flex-direction:column;align-items:center;text-align:center;color:#fff;padding:62px 54px 0;background:#08457f;overflow:hidden}.login-story:before{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(5,60,111,.22) 0%,rgba(5,44,84,0) 60%);z-index:1}.login-copy{position:relative;z-index:2;max-width:520px}.school-mark{width:66px;height:66px;margin:0 auto 22px;border-radius:20px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);display:grid;place-items:center}.school-mark .material-symbols-outlined{font-size:36px}.login-eyebrow{color:#91c7f4;font-size:13px;font-weight:600;letter-spacing:.11em}.login-story h1{font-size:34px;line-height:1.15;margin:10px 0 10px;letter-spacing:-1px}.login-story p{margin:0;color:#d8e9f7;font-size:14px}.login-benefits{position:relative;z-index:2;width:100%;display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:28px}.login-benefit{padding:13px 8px;border-radius:13px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.14)}.login-benefit span{display:grid;place-items:center;width:35px;height:35px;margin:0 auto 7px;border-radius:10px;background:#fff;color:#ef3f4c}.login-benefit:nth-child(2) span{color:#12a868}.login-benefit:nth-child(3) span{color:#db9811}.login-benefit b{display:block;font-size:11px}.login-benefit small{display:block;color:#bfd6e8;font-size:9px;margin-top:3px}.login-illustration{position:absolute;z-index:0;left:0;right:0;bottom:0;width:100%;height:46%;object-fit:cover;object-position:center bottom}.login-panel{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:50px}.login-box{width:min(390px,100%)}.login-lock{width:58px;height:58px;margin:0 auto 18px;border-radius:50%;background:#ffe5e7;color:#ef3f4c;display:grid;place-items:center}.login-box h2{text-align:center;font-size:25px;margin:0;color:var(--ink)}.login-intro{text-align:center;color:var(--muted);font-size:13px;margin:8px 0 28px}.field-wrap{position:relative}.field-wrap .material-symbols-outlined{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:18px;color:#98a2b3}.field-wrap input{height:47px;padding-left:43px;background:#f5f8fc}.password-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:#98a2b3;cursor:pointer}.login-options{display:flex;align-items:center;justify-content:space-between;margin-top:3px}.remember{display:flex;align-items:center;gap:8px;font-weight:500;font-size:12px}.remember input{width:15px;min-height:auto}.login-link{color:var(--primary);font-size:12px;font-weight:600}.login-submit{width:100%;background:#fb3545;margin-top:4px;text-transform:uppercase;letter-spacing:.04em}.login-footer{text-align:center;color:#98a2b3;font-size:11px;margin-top:26px}
        @media(max-width:900px){.topbar{display:none}.side{background:var(--navy)}.main{padding:18px}.login-page{padding:0}.login-frame{min-height:100vh;border-radius:0;grid-template-columns:1fr}.login-story{display:none}.login-panel{padding:32px 22px}.cards-grid{grid-template-columns:repeat(auto-fill,minmax(230px,1fr))}}
        /* Keep the password visibility control independent from the leading field icon. */
        .field-wrap>.material-symbols-outlined{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:18px;color:#98a2b3}
        .field-wrap input[type="password"],.field-wrap input#login-password{padding-right:52px}
        .field-wrap .password-toggle{position:absolute;z-index:2;right:7px;left:auto;top:50%;transform:translateY(-50%);width:40px;height:40px;padding:0;display:grid;place-items:center;border:0;border-radius:8px;background:transparent;color:#98a2b3;cursor:pointer;line-height:1}
        .field-wrap .password-toggle:hover{background:#e5edf8;color:#667085}
        .field-wrap .password-toggle .material-symbols-outlined{position:static;display:block;transform:none;font-size:20px;line-height:1;color:inherit}
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
        <nav class="nav">
            @auth
                @unless(auth()->user()->hasRole('Siswa'))
                    <div class="nav-section">Menu Utama</div>
                    <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? 'fill' : '' }}">dashboard</span>Dashboard</a>
                    @if(auth()->user()->hasRole('Admin'))
                        <div class="nav-section">Data Master</div>
                        <a class="{{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('students.*') ? 'fill' : '' }}">groups</span>Data Siswa</a>
                        <a class="{{ request()->routeIs('classes.*') ? 'active' : '' }}" href="{{ route('classes.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('classes.*') ? 'fill' : '' }}">class</span>Data Kelas</a>
                        <a class="{{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('users.*') ? 'fill' : '' }}">badge</span>Data Pengguna</a>
                        <a class="{{ request()->routeIs('subjects.*') ? 'active' : '' }}" href="{{ route('subjects.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('subjects.*') ? 'fill' : '' }}">menu_book</span>Data Mapel</a>
                    @endif
                    @if(auth()->user()->hasAnyRole(['Admin','Guru']))
                        <div class="nav-section">Absensi</div>
                        <a class="{{ request()->routeIs('scan.internal') ? 'active' : '' }}" href="{{ route('scan.internal') }}"><span class="material-symbols-outlined {{ request()->routeIs('scan.internal') ? 'fill' : '' }}">qr_code_scanner</span>Scan Barcode</a>
                        <a class="{{ request()->routeIs('attendance.manual') ? 'active' : '' }}" href="{{ route('attendance.manual') }}"><span class="material-symbols-outlined {{ request()->routeIs('attendance.manual') ? 'fill' : '' }}">edit_calendar</span>Absensi Manual</a>
                    @endif
                    @if(auth()->user()->hasRole('Admin'))
                        <div class="nav-section">Kartu Siswa</div>
                        <a class="{{ request()->routeIs('cards.index') ? 'active' : '' }}" href="{{ route('cards.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('cards.index') ? 'fill' : '' }}">qr_code_2</span>Barcode / QR Siswa</a>
                    @endif
                    <div class="nav-section">Laporan</div>
                    <a class="{{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('reports.*') ? 'fill' : '' }}">assessment</span>Laporan Absensi</a>
                    @if(auth()->user()->hasAnyRole(['Admin','Guru']))
                        <div class="nav-section">Akademik</div>
                        <a class="{{ request()->routeIs('grades.*') ? 'active' : '' }}" href="{{ route('grades.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('grades.*') ? 'fill' : '' }}">grade</span>Nilai Siswa</a>
                        <a class="{{ request()->routeIs('promotion.*') ? 'active' : '' }}" href="{{ route('promotion.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('promotion.*') ? 'fill' : '' }}">moving</span>Kenaikan Kelas</a>
                    @endif
                    @if(auth()->user()->hasRole('Admin'))
                        <div class="nav-section">Pengaturan</div>
                        <a class="{{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}"><span class="material-symbols-outlined {{ request()->routeIs('settings.*') ? 'fill' : '' }}">tune</span>Jam Masuk &amp; Hari Aktif</a>
                    @endif
                @else
                    <div class="nav-section">Menu Siswa</div>
                    <a class="{{ request()->routeIs('student.history') ? 'active' : '' }}" href="{{ route('student.history') }}"><span class="material-symbols-outlined {{ request()->routeIs('student.history') ? 'fill' : '' }}">history</span>Riwayat Saya</a>
                    <div class="nav-section">Kartu Siswa</div>
                    <a class="{{ request()->routeIs('cards.mine') ? 'active' : '' }}" href="{{ route('cards.mine') }}"><span class="material-symbols-outlined {{ request()->routeIs('cards.mine') ? 'fill' : '' }}">qr_code_2</span>Kartu Barcode Saya</a>
                @endunless
            @else
                <a href="{{ route('login') }}"><span class="material-symbols-outlined">login</span>Login</a>
            @endauth
        </nav>
        <div class="nav-footer">
            <a href="{{ route('scan.public') }}"><span class="material-symbols-outlined">qr_code_scanner</span>Scan Mandiri</a>
        </div>
    </aside>
    <main class="main">
        @auth
        <header class="topbar">
            <div><div class="topbar-title">{{ $title ?? 'Dashboard' }}</div><div class="topbar-crumb">Dashboard / {{ $title ?? 'Dashboard' }}</div></div>
            <div class="topbar-clock"><span class="material-symbols-outlined">schedule</span><span id="live-clock">{{ now()->translatedFormat('H.i.s') }} WIB {{ now()->translatedFormat('l, d F Y') }}</span></div>
            <div class="topbar-profile" id="user-menu">
                <button class="topbar-user" type="button" aria-haspopup="true" aria-expanded="false" onclick="toggleUserMenu()">
                    <div class="topbar-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
                    <div><div class="topbar-name">{{ auth()->user()->name }}</div><div class="topbar-role">{{ auth()->user()->roles->pluck('name')->first() }}</div></div>
                    <span class="material-symbols-outlined">expand_more</span>
                </button>
                <div class="user-dropdown" role="menu">
                    <div class="dropdown-identity"><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->email ?? auth()->user()->username }}</span></div>
                    <form method="post" action="{{ route('logout') }}">@csrf<button class="dropdown-logout" type="submit" role="menuitem"><span class="material-symbols-outlined">logout</span>Keluar dari aplikasi</button></form>
                </div>
            </div>
        </header>
        @endauth
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
<script>
    function toggleUserMenu() {
        const menu = document.getElementById('user-menu');
        if (!menu) return;
        const isOpen = menu.classList.toggle('open');
        menu.querySelector('.topbar-user')?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    document.addEventListener('click', (event) => {
        const menu = document.getElementById('user-menu');
        if (menu && !menu.contains(event.target)) {
            menu.classList.remove('open');
            menu.querySelector('.topbar-user')?.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const menu = document.getElementById('user-menu');
        menu?.classList.remove('open');
        menu?.querySelector('.topbar-user')?.setAttribute('aria-expanded', 'false');
    });

    (() => {
        const clock = document.getElementById('live-clock');
        if (!clock) return;
        const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const tick = () => { const d = new Date(); clock.textContent = `${String(d.getHours()).padStart(2,'0')}.${String(d.getMinutes()).padStart(2,'0')}.${String(d.getSeconds()).padStart(2,'0')} WIB ${days[d.getDay()]}, ${String(d.getDate()).padStart(2,'0')} ${months[d.getMonth()]} ${d.getFullYear()}`; };
        tick(); setInterval(tick, 1000);
    })();
</script>
</body>
</html>
