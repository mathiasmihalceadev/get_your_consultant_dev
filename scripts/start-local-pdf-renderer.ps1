param(
    [switch] $Foreground
)

$ErrorActionPreference = "Stop"

$root = Resolve-Path (Join-Path $PSScriptRoot "..")
$rendererDir = Join-Path $root "vps-pdf-renderer"
$logDir = Join-Path $root "storage/logs"
$outLog = Join-Path $logDir "local-pdf-renderer.out.log"
$errLog = Join-Path $logDir "local-pdf-renderer.err.log"

$chromeCandidates = @(
    "$env:USERPROFILE\.cache\puppeteer\chrome\win64-146.0.7680.153\chrome-win64\chrome.exe",
    "$env:USERPROFILE\.cache\puppeteer\chrome\win64-131.0.6778.85\chrome-win64\chrome.exe"
)

$chromePath = $chromeCandidates | Where-Object { Test-Path $_ } | Select-Object -First 1

if (-not $chromePath) {
    throw "Could not find a local Puppeteer Chrome executable. Run: npx puppeteer browsers install chrome"
}

New-Item -ItemType Directory -Force -Path $logDir | Out-Null
Remove-Item -ErrorAction SilentlyContinue $outLog, $errLog

$env:PUPPETEER_EXECUTABLE_PATH = $chromePath

if ($Foreground) {
    Write-Host "Starting local PDF renderer in foreground on http://127.0.0.1:3000"
    Write-Host "Chrome: $chromePath"
    Push-Location $rendererDir
    try {
        node server.js
    } finally {
        Pop-Location
    }
    exit
}

$process = Start-Process `
    -FilePath "node" `
    -ArgumentList "server.js" `
    -WorkingDirectory $rendererDir `
    -WindowStyle Hidden `
    -RedirectStandardOutput $outLog `
    -RedirectStandardError $errLog `
    -PassThru

Start-Sleep -Seconds 3

$check = Test-NetConnection 127.0.0.1 -Port 3000

if (-not $check.TcpTestSucceeded) {
    if (-not $process.HasExited) {
        Stop-Process -Id $process.Id -Force
    }

    Write-Host "Renderer stdout:"
    if (Test-Path $outLog) { Get-Content $outLog }
    Write-Host "Renderer stderr:"
    if (Test-Path $errLog) { Get-Content $errLog }

    throw "Local PDF renderer did not start on 127.0.0.1:3000."
}

Write-Host "Local PDF renderer started on http://127.0.0.1:3000"
Write-Host "PID: $($process.Id)"
Write-Host "Chrome: $chromePath"
Write-Host "Logs:"
Write-Host "  $outLog"
Write-Host "  $errLog"
