<div class="{{ ($public ?? false) ? 'public-main' : 'grid two' }}">
    <div class="scan-box">
        <div id="reader" style="width:100%; min-height:320px; display:grid; place-items:center; color:var(--muted)">
            <span class="material-symbols-outlined" style="font-size:64px">videocam</span>
        </div>
    </div>
    <div class="result-card grid" style="gap:16px">
        <div>
            <label style="display:block;margin-bottom:6px;font-weight:600">Input barcode scanner USB / Manual</label>
            <div style="display:flex;gap:8px">
                <input id="barcode-input" placeholder="Scan atau ketik kode barcode" autocomplete="off" style="flex:1">
                <button class="btn" id="submit-code" type="button" style="flex:0 0 auto"><span class="material-symbols-outlined">how_to_reg</span>Catat Absensi</button>
            </div>
        </div>
        <div id="scan-result" class="result-card" style="background:var(--surface-low);border:1px dashed var(--hairline)">
            <div style="display:flex;align-items:center;gap:12px;color:var(--muted);padding:8px 4px">
                <span class="material-symbols-outlined" style="font-size:32px;color:var(--outline)">qr_code_scanner</span>
                <div>
                    <div style="font-weight:600;color:var(--body)">Siap memindai kartu barcode...</div>
                    <div style="font-size:12px;color:var(--muted)">Hari, tanggal, jam, dan keterangan absensi akan muncul otomatis di sini.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
const result = document.getElementById('scan-result');
let isSubmitting = false;

async function submitCode(code){
    if(!code || isSubmitting) return;
    isSubmitting = true;

    try {
        const res = await fetch('{{ route('scan.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ barcode_value: code.trim() })
        });
        const data = await res.json().catch(() => ({ ok: false, message: 'Gagal membaca respons.' }));

        if (data.ok) {
            const isLate = data.status === 'Terlambat';
            const badgeBg = isLate ? '#fff3dc' : '#e7f8ef';
            const badgeColor = isLate ? '#9a5b13' : '#146c43';
            const badgeBorder = isLate ? '#fbd38d' : '#a3e635';
            const iconName = isLate ? 'warning' : 'check_circle';

            result.innerHTML = `
                <div style="display:flex;flex-direction:column;gap:14px">
                    <div style="display:flex;gap:14px;align-items:center;border-bottom:1px solid var(--hairline);padding-bottom:12px;flex-wrap:wrap">
                        <div style="width:54px;height:54px;border-radius:50%;background:#e5efff;color:var(--navy);display:grid;place-items:center;flex:0 0 auto">
                            <span class="material-symbols-outlined fill" style="font-size:28px">person</span>
                        </div>
                        <div style="flex:1;min-width:180px">
                            <h2 style="margin:0;font-size:20px;font-weight:700;line-height:1.2;color:var(--ink)">${data.student}</h2>
                            <div style="font-size:12px;color:var(--muted);margin-top:2px">NISN: ${data.nisn || '-'} • Kelas: <strong>${data.class}</strong></div>
                        </div>
                        <div class="status-badge" style="background:${badgeBg};color:${badgeColor};border:1px solid ${badgeBorder};padding:6px 12px;font-size:13px;font-weight:700">
                            <span class="material-symbols-outlined fill" style="font-size:18px">${iconName}</span>
                            ${data.keterangan || data.status}
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(110px,1fr));gap:10px">
                        <div style="background:#fff;padding:10px 12px;border-radius:10px;border:1px solid var(--hairline);text-align:center">
                            <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;font-weight:600">Hari</div>
                            <div style="font-size:15px;font-weight:700;color:var(--ink);margin-top:3px">${data.day || '-'}</div>
                        </div>
                        <div style="background:#fff;padding:10px 12px;border-radius:10px;border:1px solid var(--hairline);text-align:center">
                            <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;font-weight:600">Tanggal</div>
                            <div style="font-size:14px;font-weight:700;color:var(--ink);margin-top:3px">${data.date || '-'}</div>
                        </div>
                        <div style="background:#fff;padding:10px 12px;border-radius:10px;border:1px solid var(--hairline);text-align:center">
                            <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;font-weight:600">Jam</div>
                            <div style="font-size:14px;font-weight:700;color:var(--ink);margin-top:3px">
                                ${data.time ? 'Masuk ' + data.time : ''}${data.check_out ? (data.time ? '<br>' : '') + 'Pulang ' + data.check_out : ''}${!data.time && !data.check_out ? (data.scan_time || '-') : ''}
                            </div>
                        </div>
                        <div style="background:#fff;padding:10px 12px;border-radius:10px;border:1px solid var(--hairline);text-align:center">
                            <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;font-weight:600">Keterangan</div>
                            <div style="font-size:13px;font-weight:700;color:${badgeColor};margin-top:3px">${data.keterangan || data.status}</div>
                        </div>
                    </div>

                    <div style="padding:10px 12px;border-radius:8px;background:${badgeBg};color:${badgeColor};font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px">
                        <span class="material-symbols-outlined" style="font-size:18px">${iconName}</span>
                        <span>${data.message}</span>
                    </div>
                </div>
            `;
        } else {
            result.innerHTML = `
                <div style="padding:14px;border-radius:10px;border:1px solid rgba(239,68,68,.3);background:#fef2f2;color:var(--error);display:flex;align-items:center;gap:10px">
                    <span class="material-symbols-outlined" style="font-size:24px;flex:0 0 auto">error</span>
                    <div>
                        <div style="font-weight:700;font-size:14px">Scan Gagal</div>
                        <div style="font-size:13px;margin-top:2px">${data.message || 'Barcode/QR tidak valid.'}</div>
                    </div>
                </div>
            `;
        }
    } catch(e) {
        result.innerHTML = `
            <div style="padding:14px;border-radius:10px;border:1px solid rgba(239,68,68,.3);background:#fef2f2;color:var(--error);display:flex;align-items:center;gap:10px">
                <span class="material-symbols-outlined" style="font-size:24px;flex:0 0 auto">cloud_off</span>
                <div>
                    <div style="font-weight:700;font-size:14px">Koneksi Bermasalah</div>
                    <div style="font-size:13px;margin-top:2px">Tidak dapat menghubungi server absensi.</div>
                </div>
            </div>
        `;
    } finally {
        setTimeout(() => { isSubmitting = false; }, 1200);
    }
}
document.getElementById('submit-code').onclick=()=>{
    const input = document.getElementById('barcode-input');
    submitCode(input.value);
    input.value = '';
    input.focus();
};
document.getElementById('barcode-input').addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();submitCode(e.target.value);e.target.value='';}});
if(window.Html5QrcodeScanner){new Html5QrcodeScanner('reader',{fps:10,qrbox:220}).render(decoded=>submitCode(decoded));}
</script>
@endpush
