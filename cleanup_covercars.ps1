[CmdletBinding()]
param([switch]$DryRun)

$ErrorActionPreference = 'Stop'

# --- Rutas base ---
$root = (Resolve-Path ".").Path
if ($root -like '*\covercars') {
    # Ya estamos dentro de covercars
    $newRoot = $root
    $projectRoot = Split-Path $root -Parent
} else {
    $newRoot = Join-Path $root 'covercars'
    $projectRoot = $root
}

# --- Carpeta y archivo de log ---
$logDir = Join-Path $newRoot '_cleanup_logs'
if (-not (Test-Path $logDir)) { New-Item -ItemType Directory -Force -Path $logDir | Out-Null }

$logFile = Join-Path $logDir ("cleanup_{0:yyyyMMdd_HHmmss}.log" -f (Get-Date))

# --- Funciones auxiliares ---
function Log([string]$msg) {
    Write-Host $msg
    if (-not $DryRun) { $msg | Out-File -FilePath $logFile -Append -Encoding utf8 }
}

function Move-Safe($src,$dst) {
    if (-not (Test-Path $src)) { Log "MISS: $src"; return }
    $dstDir = Split-Path $dst -Parent
    if (-not (Test-Path $dstDir)) { if (-not $DryRun) { New-Item -ItemType Directory -Force -Path $dstDir | Out-Null } }
    Log ("MOVE: $src -> $dst")
    if (-not $DryRun) { Move-Item -LiteralPath $src -Destination $dst -Force }
}

function Remove-Safe($item) {
    if (-not (Test-Path $item)) { return }
    Log ("DEL : $item")
    if (-not $DryRun) { Remove-Item -LiteralPath $item -Recurse -Force }
}

# ----------  INICIO ----------
Log "=== LIMPIEZA covercars ==="
Log ("Dry-run: " + ($(if ($DryRun) { 'SÍ' } else { 'NO' })))
