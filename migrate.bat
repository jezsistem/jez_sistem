@echo off
REM Core HR tables
php artisan migrate --path=database/migrations/2025_09_12_152050_create_online_transaction_chat_history_table.php || exit 1
php artisan migrate --path=database/migrations/2025_09_13_103549_add_otd_id_column_to_product_location_setup_transactions_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_09_143458_add_files_resi_to_online_transactions_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_09_151323_create_split_resi_logs_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_09_203727_create_warehouse_index_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_10_125821_add_warehouse_column_to_online_transaction_details_table.php || exit 1

php artisan migrate --path=database/migrations/2025_10_14_183751_add_internal_order_status_to_online_transactions.php || exit 1
php artisan migrate --path=database/migrations/2025_10_14_184740_modify_enum_column_plst_status_in_product_location_setup_transactions_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_14_205642_add_warehouse_st_id_in_product_location_setup_transactions_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_18_165246_add_column_qc_status_to_product_location_setup_transactions_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_18_212923_add_soft_deletes_and_audit_columns_to_online_transaction_details_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_21_203945_add_online_done_into_pls_status_and_internal_order_status.php || exit 1
php artisan migrate --path=database/migrations/2025_10_22_100630_add_courier_to_online_transactions_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_22_113847_add_manifest_status_to_online_transactions_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_23_154304_create_delivery_recaps_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_23_174840_create_delivery_receipts_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_27_100333_add_column_pl_default_failed_qc_to_product_locations_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_27_123406_add_failed_qc_to_product_location_setup_transactions_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_29_200753_add_print_resi_status_to_online_transactions_table.php || exit 1
php artisan migrate --path=database/migrations/2025_10_31_012129_add_manifest_to_ts_delivery_recaps_table.php || exit 1

pause
