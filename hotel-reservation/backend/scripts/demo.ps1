param([int]$Port = 8000)
$ErrorActionPreference = 'Stop'
Set-Location (Join-Path $PSScriptRoot '..')
if (Test-Path bootstrap/cache/config.php) { throw 'Remove cached configuration before using the isolated demo.' }
$demoPath = Join-Path (Get-Location) ('database/demo-' + [guid]::NewGuid().ToString() + '.sqlite')
if (Test-Path -LiteralPath $demoPath) { throw 'Refusing to reuse an existing database.' }
New-Item -ItemType File -Path $demoPath | Out-Null
$env:APP_ENV = 'local'
$env:APP_DEBUG = 'false'
$env:APP_KEY = 'base64:' + [Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes([guid]::NewGuid().ToString('N')))
$env:APP_URL = "http://127.0.0.1:$Port"
$env:DB_URL = ''
$env:DB_CONNECTION = 'sqlite'
$env:DB_DATABASE = $demoPath
$env:SESSION_DRIVER = 'file'
$env:CACHE_STORE = 'array'
$env:QUEUE_CONNECTION = 'sync'
Write-Output "Isolated database: sqlite / $demoPath"
php artisan migrate --force --no-interaction
if ($LASTEXITCODE -ne 0) { throw 'Demo migration failed.' }
php artisan db:seed --class=DemoSeeder --force --no-interaction
if ($LASTEXITCODE -ne 0) { throw 'Demo seeding failed.' }
php artisan serve --host=127.0.0.1 --port=$Port
