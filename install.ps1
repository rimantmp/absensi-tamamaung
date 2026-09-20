param([switch]$CheckOnly)

$ErrorActionPreference = 'Stop'
$projectDir = $PSScriptRoot

function Step($m) { Write-Host "`n==> $m" -ForegroundColor Cyan }
function Ok($m) { Write-Host "[OK] $m" -ForegroundColor Green }
function Warn($m) { Write-Host "[PERHATIAN] $m" -ForegroundColor Yellow }
function Fail($m) { Write-Host "[GAGAL] $m" -ForegroundColor Red; exit 1 }
function Ask($m, [bool]$yes = $true) {
    $label = if ($yes) { 'Y/n' } else { 'y/N' }
    while ($true) {
        $a = Read-Host "$m ($label)"
        if ([string]::IsNullOrWhiteSpace($a)) { return $yes }
        if ($a -match '^(y|ya)$') { return $true }
        if ($a -match '^(n|tidak)$') { return $false }
    }
}
function Ask-Default($m, $default) {
    $a = Read-Host "$m [$default]"
    if ([string]::IsNullOrWhiteSpace($a)) { return $default }
    return $a.Trim()
}
function Env-Quote($value) { return '"' + $value.Replace('\', '\\').Replace('"', '\"') + '"' }
function Set-Env($path, [hashtable]$values) {
    $left = @{}; foreach ($key in $values.Keys) { $left[$key] = $values[$key] }
    $lines = New-Object System.Collections.Generic.List[string]
    foreach ($line in [IO.File]::ReadAllLines($path)) {
        $match = [regex]::Match($line, '^\s*#?\s*([A-Z][A-Z0-9_]*)=')
        if ($match.Success -and $left.ContainsKey($match.Groups[1].Value)) {
            $key = $match.Groups[1].Value; $lines.Add("$key=$($left[$key])"); $left.Remove($key)
        } else { $lines.Add($line) }
    }
    foreach ($key in $left.Keys) { $lines.Add("$key=$($left[$key])") }
    [IO.File]::WriteAllLines($path, $lines, (New-Object Text.UTF8Encoding($false)))
}
function Find-PHP {
    $found = @()
    $command = Get-Command php -ErrorAction SilentlyContinue
    if ($command) { $found += $command.Source }
    foreach ($root in @('C:\laragon\bin\php', 'C:\xampp\php', 'C:\wamp64\bin\php', 'C:\wamp\bin\php')) {
        if (Test-Path $root) { $found += Get-ChildItem $root -Recurse -Filter php.exe -ErrorAction SilentlyContinue | Sort-Object FullName -Descending | ForEach-Object FullName }
    }
    return $found | Where-Object { Test-Path $_ } | Select-Object -Unique | Select-Object -First 1
}
function Find-Composer {
    $command = Get-Command composer -ErrorAction SilentlyContinue
    if ($command) { return @{ Path = $command.Source; Phar = $false } }
    $local = Join-Path $projectDir 'composer.phar'
    if (Test-Path $local) { return @{ Path = $local; Phar = $true } }
    $item = Get-ChildItem 'C:\laragon\bin\composer' -Recurse -Filter composer.phar -ErrorAction SilentlyContinue | Sort-Object FullName -Descending | Select-Object -First 1
    if ($item) { return @{ Path = $item.FullName; Phar = $true } }
    return $null
}
function Run-Composer($info, $php) {
    if ($info.Phar) { & $php $info.Path install --no-interaction --prefer-dist --optimize-autoloader }
    else { & $info.Path install --no-interaction --prefer-dist --optimize-autoloader }
    if ($LASTEXITCODE -ne 0) { Fail 'Composer gagal. Periksa koneksi internet dan pesan di atas.' }
}
function Prepare-Database($php, $hostName, $port, $name, $user, $password) {
    $temp = Join-Path ([IO.Path]::GetTempPath()) ("absensi-db-$([guid]::NewGuid()).php")
    $code = @'
<?php
$h=getenv('I_DB_HOST');$p=getenv('I_DB_PORT');$n=getenv('I_DB_NAME');$u=getenv('I_DB_USER');$w=getenv('I_DB_PASS');
try {
 $pdo=new PDO("mysql:host=$h;port=$p;charset=utf8mb4",$u,$w,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_TIMEOUT=>10]);
 $q=$pdo->prepare('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME=?');$q->execute([$n]);
 if($q->fetchColumn()){echo 'EXISTS';exit;}
 if(!preg_match('/^[A-Za-z0-9_]+$/',$n)){throw new RuntimeException('Nama database hanya boleh berisi huruf, angka, dan underscore.');}
 $pdo->exec("CREATE DATABASE `$n` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");echo 'CREATED';
}catch(Throwable $e){fwrite(STDERR,$e->getMessage());exit(1);}
'@
    [IO.File]::WriteAllText($temp, $code, (New-Object Text.UTF8Encoding($false)))
    try {
        $env:I_DB_HOST=$hostName;$env:I_DB_PORT=$port;$env:I_DB_NAME=$name;$env:I_DB_USER=$user;$env:I_DB_PASS=$password
        $result = & $php $temp 2>&1
        if ($LASTEXITCODE -ne 0) { Fail "Database gagal disiapkan: $($result -join ' ')" }
        return ($result -join '').Trim()
    } finally {
        Remove-Item Env:I_DB_HOST,Env:I_DB_PORT,Env:I_DB_NAME,Env:I_DB_USER,Env:I_DB_PASS -ErrorAction SilentlyContinue
        Remove-Item -LiteralPath $temp -Force -ErrorAction SilentlyContinue
    }
}

Clear-Host
Write-Host '=================================================='
Write-Host ' AUTO INSTALLER - ABSENSI SD INPRES TAMAMAUNG IV'
Write-Host '=================================================='
if (-not (Test-Path (Join-Path $projectDir 'artisan'))) { Fail 'Jalankan installer dari folder project.' }

Step 'Memeriksa PHP 8.3+'
$php = Find-PHP
if (-not $php) { Fail 'PHP tidak ditemukan. Aktifkan Laragon atau install PHP 8.3+.' }
$versionId = [int](& $php -r 'echo PHP_VERSION_ID;')
if ($versionId -lt 80300) { Fail "PHP $(& $php -r 'echo PHP_VERSION;') terlalu lama. Gunakan PHP 8.3+." }
$env:PATH = (Split-Path $php) + ';' + $env:PATH
Ok "PHP $(& $php -r 'echo PHP_VERSION;') - $php"

Step 'Memeriksa ekstensi PHP'
$modules = @(& $php -m)
$required = @('curl','dom','fileinfo','gd','mbstring','openssl','pdo_mysql','xml','zip')
$missing = @($required | Where-Object { $wanted=$_; -not ($modules | Where-Object { $_.Trim() -ieq $wanted }) })
if ($missing.Count) { Fail "Aktifkan ekstensi pada php.ini: $($missing -join ', ')" }
Ok 'Ekstensi PHP lengkap.'

Step 'Memeriksa Composer'
$composer = Find-Composer
if (-not $composer) { Fail 'Composer tidak ditemukan. Install Composer atau letakkan composer.phar di folder project.' }
Ok "Composer ditemukan: $($composer.Path)"
if ($CheckOnly) { Ok 'Komputer siap menjalankan installer.'; exit 0 }

$envFile = Join-Path $projectDir '.env'; $envExample = Join-Path $projectDir '.env.example'
$newEnv = -not (Test-Path $envFile)
if ($newEnv) {
    if (-not (Test-Path $envExample)) { Fail '.env.example tidak ditemukan.' }
    Copy-Item -LiteralPath $envExample -Destination $envFile
    Ok '.env berhasil dibuat.'
} else { Ok '.env sudah ada dan dipertahankan.' }

$configureDb = $newEnv -or (Ask 'Perbarui konfigurasi database MySQL?' $false)
$dbState = 'EXISTS'
if ($configureDb) {
    Step 'Menyiapkan database MySQL'
    $dbHost=Ask-Default 'Host MySQL' '127.0.0.1';$dbPort=Ask-Default 'Port MySQL' '3306'
    $dbName=Ask-Default 'Nama database' 'absensi_tamamaung';$dbUser=Ask-Default 'Username MySQL' 'root'
    $secure=Read-Host 'Password MySQL (kosong untuk Laragon standar)' -AsSecureString
    $dbPass=(New-Object Net.NetworkCredential('', $secure)).Password
    $dbState=Prepare-Database $php $dbHost $dbPort $dbName $dbUser $dbPass
    Set-Env $envFile @{APP_NAME=(Env-Quote 'Absensi SD Inpres Tamamaung IV');DB_CONNECTION='mysql';DB_HOST=$dbHost;DB_PORT=$dbPort;DB_DATABASE=$dbName;DB_USERNAME=$dbUser;DB_PASSWORD=(Env-Quote $dbPass)}
    if ($dbState -eq 'CREATED') { Ok "Database '$dbName' dibuat." } else { Ok "Database '$dbName' sudah ada; data lama dipertahankan." }
}

$production = Ask 'Gunakan mode produksi agar aplikasi lebih ringan?' $true
if ($production) { Set-Env $envFile @{APP_ENV='production';APP_DEBUG='false';LOG_LEVEL='warning'} }
else { Set-Env $envFile @{APP_ENV='local';APP_DEBUG='true';LOG_LEVEL='debug'} }

Push-Location $projectDir
try {
    Step 'Memasang dependency PHP'; Run-Composer $composer $php
    if ([IO.File]::ReadAllText($envFile) -notmatch '(?m)^APP_KEY=base64:.+$') {
        Step 'Membuat application key'; & $php artisan key:generate --force
        if ($LASTEXITCODE -ne 0) { Fail 'APP_KEY gagal dibuat.' }
    } else { Ok 'APP_KEY sudah tersedia.' }

    Step 'Menjalankan migrasi database'
    & $php artisan optimize:clear
    & $php artisan migrate --force --no-interaction
    if ($LASTEXITCODE -ne 0) { Fail 'Migrasi database gagal.' }

    if (Ask 'Isi database dengan akun dan data demo?' ($dbState -eq 'CREATED')) {
        & $php artisan db:seed --force --no-interaction
        if ($LASTEXITCODE -ne 0) { Fail 'Seeder gagal.' }
    }

    Step 'Menyiapkan storage dan cache'
    & $php artisan storage:link 2>$null
    if ($production) { & $php artisan optimize } else { & $php artisan optimize:clear }
    if ($LASTEXITCODE -ne 0) { Fail 'Cache aplikasi gagal dibuat.' }
} finally { Pop-Location }

Write-Host "`n==================================================" -ForegroundColor Green
Write-Host ' INSTALASI BERHASIL' -ForegroundColor Green
Write-Host '==================================================' -ForegroundColor Green
Write-Host 'Database dan .env lama tidak dihapus oleh installer.'
Write-Host 'Di Laragon: klik Reload, lalu buka project dari menu Laragon.'
if (Ask 'Buka folder project sekarang?' $false) { Start-Process -FilePath explorer.exe -ArgumentList $projectDir }
