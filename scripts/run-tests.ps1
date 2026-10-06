$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
Push-Location $root
try {
    php .\tests\run.php
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
    Write-Host "`nLinting PHP files..."
    $files = Get-ChildItem -Path . -Recurse -Filter *.php | Where-Object { $_.FullName -notmatch '\\vendor\\' }
    foreach ($file in $files) {
        php -l $file.FullName | Out-Host
        if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
    }
    Write-Host "`nAWare Support Workbench test suite passed."
}
finally { Pop-Location }
