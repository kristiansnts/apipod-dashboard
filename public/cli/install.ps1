# apipod-cli installer for Windows
$ErrorActionPreference = "Stop"

$DashboardUrl = if ($env:APIPOD_DASHBOARD_URL) { $env:APIPOD_DASHBOARD_URL } else { "https://apipod.app" }
$InstallDir = if ($env:APIPOD_INSTALL_DIR) { $env:APIPOD_INSTALL_DIR } else { Join-Path $env:USERPROFILE ".apipod" }
$BinDir = Join-Path $InstallDir "bin"

function Write-Info  { param($msg) Write-Host "  ▶ $msg" -ForegroundColor Cyan }
function Write-Ok    { param($msg) Write-Host "  ✓ $msg" -ForegroundColor Green }
function Write-Err   { param($msg) Write-Host "  ✗ $msg" -ForegroundColor Red; exit 1 }

Write-Host ""
Write-Host "  ◆ apipod-cli installer" -ForegroundColor Cyan -NoNewline
Write-Host ""
Write-Host ""

# Check Node.js
try {
    $nodeVersion = (node -v 2>$null)
    if (-not $nodeVersion) { throw "not found" }
    $major = [int]($nodeVersion -replace 'v(\d+)\..*', '$1')
    if ($major -lt 18) { Write-Err "Node.js 18+ is required (found $nodeVersion)" }
    Write-Ok "Node.js $nodeVersion"
} catch {
    Write-Err "Node.js is required. Install it from https://nodejs.org"
}

# Check npm
try {
    npm -v 2>$null | Out-Null
} catch {
    Write-Err "npm is required"
}

# Create install directory
Write-Info "Installing to $InstallDir"
New-Item -ItemType Directory -Path $BinDir -Force | Out-Null

# Download
$TempDir = Join-Path ([System.IO.Path]::GetTempPath()) "apipod-cli-$(Get-Random)"
New-Item -ItemType Directory -Path $TempDir -Force | Out-Null

try {
    Write-Info "Downloading apipod-cli from $DashboardUrl..."

    $ZipUrl = "$DashboardUrl/cli/download?format=zip"
    $ZipPath = Join-Path $TempDir "apipod-cli.zip"

    Invoke-WebRequest -Uri $ZipUrl -OutFile $ZipPath -UseBasicParsing
    Expand-Archive -Path $ZipPath -DestinationPath $TempDir -Force

    $SourceDir = Join-Path $TempDir "apipod-cli"
    Write-Ok "Downloaded"

    # Install dependencies
    Write-Info "Installing dependencies..."
    Push-Location $SourceDir
    npm install --production --silent 2>$null
    Pop-Location
    Write-Ok "Dependencies installed"

    # Copy to install dir
    $LibDir = Join-Path $InstallDir "lib"
    if (Test-Path $LibDir) { Remove-Item -Recurse -Force $LibDir }
    Copy-Item -Recurse $SourceDir $LibDir

    # Create launcher scripts
    # .cmd for Command Prompt
    $CmdLauncher = Join-Path $BinDir "apipod.cmd"
    @"
@echo off
node "%~dp0\..\lib\src\index.js" %*
"@ | Set-Content $CmdLauncher -Encoding ASCII

    # .ps1 for PowerShell
    $Ps1Launcher = Join-Path $BinDir "apipod.ps1"
    @"
#!/usr/bin/env pwsh
`$ScriptDir = Split-Path -Parent `$MyInvocation.MyCommand.Path
node (Join-Path `$ScriptDir "..\lib\src\index.js") @args
"@ | Set-Content $Ps1Launcher -Encoding UTF8

    Write-Ok "Installed to $InstallDir"

    # Add to PATH
    $UserPath = [Environment]::GetEnvironmentVariable("Path", "User")
    if ($UserPath -notlike "*$BinDir*") {
        [Environment]::SetEnvironmentVariable("Path", "$BinDir;$UserPath", "User")
        $env:Path = "$BinDir;$env:Path"
        Write-Ok "Added to PATH"
    } else {
        Write-Ok "PATH already configured"
    }

    Write-Host ""
    Write-Host "  ✓ apipod-cli installed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "  Run:" -ForegroundColor Gray
    Write-Host "    apipod              Launch the CLI"
    Write-Host "    apipod --help       Show help"
    Write-Host ""

    if ($env:Path -notlike "*$BinDir*") {
        Write-Host "  Restart your terminal to use apipod." -ForegroundColor Gray
        Write-Host ""
    }
} finally {
    Remove-Item -Recurse -Force $TempDir -ErrorAction SilentlyContinue
}
