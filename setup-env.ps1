# PowerShell script to copy .env.example files to .env files

$services = @(
    "data-processor-service",
    "feed-fetcher-service",
    "feed-parser-service",
    "feed-transformer-service",
    "reverb-service"
)

foreach ($service in $services) {
    $examplePath = Join-Path -Path $PSScriptRoot -ChildPath "$service/.env.example"
    $envPath = Join-Path -Path $PSScriptRoot -ChildPath "$service/.env"
    
    if (Test-Path $examplePath) {
        Write-Host "Copying $examplePath to $envPath"
        Copy-Item -Path $examplePath -Destination $envPath -Force
        
        # Generate a Laravel application key if needed
        if (!(Select-String -Path $envPath -Pattern "APP_KEY=base64:")) {
            Write-Host "Generating application key for $service"
            Set-Location -Path (Join-Path -Path $PSScriptRoot -ChildPath $service)
            php artisan key:generate
        }
    } else {
        Write-Host "Warning: $examplePath does not exist"
    }
}

Write-Host "Environment files setup complete!"
