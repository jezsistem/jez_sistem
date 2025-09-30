@echo off
cd /d %~dp0

REM 1. Core System
php artisan db:seed --class=WebConfigSeeder
php artisan db:seed --class=GroupSeeder
php artisan db:seed --class=UserGroupSeeder

REM 2. HR System
php artisan db:seed --class=UserDivisionSeeder
php artisan db:seed --class=UserPositionSeeder
php artisan db:seed --class=UserTypeSeeder
php artisan db:seed --class=ShiftCodeSeeder
php artisan db:seed --class=LeaveTypeSeeder
php artisan db:seed --class=LeaveBalanceSeeder

REM 3. Menu System
php artisan db:seed --class=HRMenuSeeder
php artisan db:seed --class=StaffMenuSeeder
php artisan db:seed --class=AttendanceMenuSeeder
php artisan db:seed --class=BreakTimeMenuSeeder
php artisan db:seed --class=LeaveMenuSeeder
php artisan db:seed --class=UserPositionMenuSeeder
php artisan db:seed --class=UserTypeMenuSeeder

REM 4. Announcement System
php artisan db:seed --class=AnnouncementCategorySeeder
php artisan db:seed --class=AnnouncementReactionSeeder
php artisan db:seed --class=AnnouncementMenuSeeder

REM 5. Users (terakhir)
php artisan db:seed --class=UserSeeder

echo.
echo All migrations and seeders executed successfully!
pause
