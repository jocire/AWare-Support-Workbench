param(
    [Parameter(Mandatory=$true)][string]$WikiRepoUrl,
    [string]$WorkDir = "$env:TEMP\aware-support-workbench-wiki"
)
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
if (Test-Path $WorkDir) { Remove-Item -Recurse -Force $WorkDir }
git clone $WikiRepoUrl $WorkDir
Copy-Item -Path (Join-Path $root 'wiki\*.md') -Destination $WorkDir -Force
Push-Location $WorkDir
try {
    git add *.md
    if (-not (git diff --cached --quiet)) {
        git commit -m "Update AWare Support Workbench wiki"
        git push
    } else {
        Write-Host 'Wiki already up to date.'
    }
}
finally { Pop-Location }
