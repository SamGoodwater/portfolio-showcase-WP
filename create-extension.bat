@echo off
setlocal enabledelayedexpansion

:: Configuration
set "EXTENSION_NAME=portfolio-showcase"
set "ZIP_FILE=%EXTENSION_NAME%.zip"
set "EXCLUDE_FILES=*.psd *.git* *.bat create-extension.bat .gitattributes .gitignore resumé.txt"

:: Supprimer l'ancien fichier ZIP s'il existe
if exist "%ZIP_FILE%" (
    echo Suppression de l'ancien fichier ZIP...
    del "%ZIP_FILE%"
)

:: Créer le nouveau fichier ZIP
echo Creation du nouveau fichier ZIP...

:: Créer un dossier temporaire avec la structure correcte
mkdir temp
xcopy /E /I includes temp\includes
xcopy /E /I assets temp\assets
xcopy /E /I languages temp\languages
copy LICENSE temp\
copy portfolio-showcase.php temp\

:: Créer le ZIP à partir du dossier temporaire
powershell Compress-Archive -Path "temp\*" -DestinationPath "%ZIP_FILE%" -Force

:: Nettoyer le dossier temporaire
rmdir /S /Q temp

echo.
echo Fichier ZIP cree avec succes : %ZIP_FILE%
echo.
pause 