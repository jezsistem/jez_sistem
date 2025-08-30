@echo off
REM Core HR tables
php artisan migrate --path=database/migrations/2024_01_01_000001_create_shift_codes_table.php
php artisan migrate --path=database/migrations/2024_01_01_000002_create_user_divisions_table.php
php artisan migrate --path=database/migrations/2024_01_01_000003_create_daily_schedules_table.php

REM Attendance & Leave
php artisan migrate --path=database/migrations/2025_08_05_101229_create_attendance_table.php
php artisan migrate --path=database/migrations/2025_08_05_120000_create_break_times_table.php
php artisan migrate --path=database/migrations/2025_08_05_122506_create_leave_types_table.php
php artisan migrate --path=database/migrations/2025_08_05_122622_create_leave_requests_table.php
php artisan migrate --path=database/migrations/2025_08_05_131129_create_leave_balances_table.php

REM User Management
php artisan migrate --path=database/migrations/2025_08_05_131129_create_ts_user_positions_table.php
php artisan migrate --path=database/migrations/2025_08_05_193923_add_position_division_to_users_table.php
php artisan migrate --path=database/migrations/2025_08_08_094253_create_user_types_table.php
php artisan migrate --path=database/migrations/2025_08_08_094349_add_user_type_to_users_table.php

REM Announcement System
php artisan migrate --path=database/migrations/2025_08_08_110811_create_announcements_table.php
php artisan migrate --path=database/migrations/2025_08_08_110927_create_announcement_categories_table.php
php artisan migrate --path=database/migrations/2025_08_08_111023_create_announcement_reactions_table.php
php artisan migrate --path=database/migrations/2025_08_08_112501_create_announcement_user_reactions_table.php
php artisan migrate --path=database/migrations/2025_08_08_112550_create_announcement_recipients_table.php
php artisan migrate --path=database/migrations/2025_08_08_131228_create_announcement_attachments_table.php
php artisan migrate --path=database/migrations/2025_08_08_142144_create_announcement_views_table.php

REM Foreign Keys
php artisan migrate --path=database/migrations/2025_08_08_120847_add_foreign_keys_to_announcements_table.php

pause
