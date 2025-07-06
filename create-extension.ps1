# Configuration
$ExtensionName = "portfolio-showcase"
$ZipFile = "$ExtensionName.zip"
$ExcludeFiles = @("*.psd", "*.git*", "create-extension.bat", ".gitattributes", ".gitignore", "résumé.txt", "*.zip", "*.ps1", "*.md")

# Supprimer l'ancien fichier ZIP s'il existe
if (Test-Path $ZipFile) {
    Write-Host "Suppression de l'ancien fichier ZIP..." -ForegroundColor Yellow
    Remove-Item $ZipFile -Force
}

# Créer le nouveau fichier ZIP
Write-Host "Création du nouveau fichier ZIP..." -ForegroundColor Green

# Créer un dossier temporaire avec la structure correcte
$TempDir = "temp"
if (Test-Path $TempDir) {
    Remove-Item $TempDir -Recurse -Force
}
New-Item -ItemType Directory -Path $TempDir | Out-Null

# Copier les dossiers et fichiers nécessaires
Write-Host "Copie des fichiers..." -ForegroundColor Cyan

# Copier le dossier includes
if (Test-Path "includes") {
    Copy-Item -Path "includes" -Destination "$TempDir\includes" -Recurse -Force
    Write-Host "✓ Dossier 'includes' copié" -ForegroundColor Green
}

# Copier le dossier assets
if (Test-Path "assets") {
    Copy-Item -Path "assets" -Destination "$TempDir\assets" -Recurse -Force
    Write-Host "✓ Dossier 'assets' copié" -ForegroundColor Green
}

# Copier le dossier languages
if (Test-Path "languages") {
    Copy-Item -Path "languages" -Destination "$TempDir\languages" -Recurse -Force
    Write-Host "✓ Dossier 'languages' copié" -ForegroundColor Green
}

# Copier les fichiers individuels
$FilesToCopy = @("LICENSE", "portfolio-showcase.php", "README.md")
foreach ($File in $FilesToCopy) {
    if (Test-Path $File) {
        Copy-Item -Path $File -Destination "$TempDir\" -Force
        Write-Host "✓ Fichier '$File' copié" -ForegroundColor Green
    }
}

# Créer le ZIP
try {
    # Essayer d'abord avec le module natif
    if (Get-Command Compress-Archive -ErrorAction SilentlyContinue) {
        Compress-Archive -Path "$TempDir\*" -DestinationPath $ZipFile -Force
        Write-Host "✓ Archive créée avec Compress-Archive" -ForegroundColor Green
    } else {
        # Fallback: utiliser .NET pour créer le ZIP
        Add-Type -AssemblyName System.IO.Compression.FileSystem
        [System.IO.Compression.ZipFile]::CreateFromDirectory($TempDir, $ZipFile)
        Write-Host "✓ Archive créée avec .NET" -ForegroundColor Green
    }
} catch {
    Write-Host "Erreur lors de la création de l'archive: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Nettoyer le dossier temporaire
if (Test-Path $TempDir) {
    Remove-Item $TempDir -Recurse -Force
    Write-Host "✓ Dossier temporaire nettoyé" -ForegroundColor Green
}

Write-Host ""
Write-Host "Fichier ZIP créé avec succès : $ZipFile" -ForegroundColor Green
Write-Host ""

# Vérifier que le fichier a été créé
if (Test-Path $ZipFile) {
    $FileSize = (Get-Item $ZipFile).Length
    $FileSizeKB = [math]::Round($FileSize / 1KB, 2)
    Write-Host "Taille du fichier : $FileSizeKB KB" -ForegroundColor Cyan
} else {
    Write-Host "Erreur : Le fichier ZIP n'a pas été créé" -ForegroundColor Red
}

Write-Host ""
Write-Host "Appuyez sur une touche pour continuer..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
