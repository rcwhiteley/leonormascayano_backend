set frontPath=C:\Users\racwh\Desktop\intranet\intranet-leonormascayano
set backPath=C:\Users\racwh\Desktop\intranet\backend-lmascayno\lmascayano-backend
cd %frontPath%

xcopy /s %frontPath%\build\index.html %backPath%\resources\views\index.blade.php /Y
xcopy /s %frontPath%\build\static %backPath%\public\static /Y
cd %backPath%
git add .
git commit -m "deploy"
git push origin master
@echo off
cls
call rake
pause
