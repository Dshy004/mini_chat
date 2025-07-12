@echo off
REM Désactive l’affichage des commandes dans la console
cd /d %~dp0
REM Se place dans le dossier où se trouve ce script
php -S localhost:8000
REM Démarre le serveur web PHP intégré sur localhost:8000
pause
REM Attend que l’utilisateur appuie sur une touche avant de fermer la fenêtre
