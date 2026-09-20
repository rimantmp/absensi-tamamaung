<div class="{{ ($public ?? false) ? 'public-main' : 'grid two' }}">
    <div class="scan-box">
        <div id="reader" style="width:100%; min-height:320px; display:grid; place-items:center; color:var(--muted)">
            <span class="material-symbols-outlined" style="font-size:64px">videocam</span>
        </div>
    </div>
    <div class="result-card grid">
        <label>Input barcode scanner USB<input id="barcode-input" placeholder="Scan atau ketik kode barcode"></label>
        <button class="btn" id="submit-code"><span class="material-symbols-outlined">how_to_reg</span>Catat Absensi</button>
        <div id="scan-result" class="result-card" style="background:var(--surface-low)">
            <div class="muted">Belum ada scan.</div>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
const result = document.getElementById('scan-result');
async function submitCode(code){
    if(!code) return;
    const res = await fetch('{{ route('scan.store') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({barcode_value:code})});
    const data = await res.json().catch(()=>({ok:false,message:'Gagal membaca respons.'}));
    result.innerHTML = data.ok
        ? `<div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap">
                <div style="width:72px;height:72px;border-radius:999px;background:var(--lav);display:grid;place-items:center;flex:0 0 auto">
                    <span class="material-symbols-outlined fill" style="font-size:36px">person</span>
                </div>
                <div style="flex:1;min-width:180px">
                    <h2 style="margin:0;font-size:24px;line-height:1.3">${data.student}</h2>
                    <div class="muted">${data.class}</div>
                </div>
                <div class="status-badge" style="border-color:${data.status === 'Hadir' ? 'var(--success)' : 'var(--ochre)'};color:${data.status === 'Hadir' ? 'var(--success)' : 'var(--ink)'}">
                    <span class="material-symbols-outlined fill">${data.created ? 'check_circle' : 'info'}</span>
                    ${data.status} - ${data.time}${data.check_out ? ' / ' + data.check_out : ''}
                </div>
           </div>
           <p class="muted" style="margin:12px 0 0">${data.message}</p>`
        : `<div class="status-badge" style="border-color:var(--error);color:var(--error)"><span class="material-symbols-outlined">error</span>${data.message}</div>`;
}
document.getElementById('submit-code').onclick=()=>submitCode(document.getElementById('barcode-input').value);
document.getElementById('barcode-input').addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();submitCode(e.target.value);e.target.value='';}});
if(window.Html5QrcodeScanner){new Html5QrcodeScanner('reader',{fps:10,qrbox:220}).render(decoded=>submitCode(decoded));}
</script>
@endpush
