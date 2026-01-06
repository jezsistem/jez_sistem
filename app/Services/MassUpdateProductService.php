<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductLogs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MassUpdateProductService
{
    public function processRowArticleLevel($row, $update_column)
    {
        $error_ids = [];
        DB::beginTransaction();
        try {
            foreach ($row as $index => $products) {
                if ($index === 0) continue; // Skip array index 0

                $data_before = DB::table('products')
                    ->where('article_id', $products[0])->first();

                $article_id = $products[0];
                $new_value = $products[1];

                $update_data = DB::table('products')
                    ->where('article_id', $article_id)
                    ->update([
                        $update_column => $new_value,
                        'updated_at' => now() // Update the updated_at timestamp
                    ]);

                if ($update_data === 0) {
                    $error_ids[] = $article_id; // Collect article IDs with errors
                }

                $productLog = new \App\Models\ProductLogs();
                $timestamp = date('Y-m-d H:i:s');
                $productLog->storeProductLog(
                    $data_before->id,
                    Auth::id(),
                    ProductLogs::LEVEL_ARTICLE,
                    $update_column,
                    '/mass-update-products',
                    $data_before->$update_column,
                    $new_value,
                    $timestamp
                );
            }

            if (empty($error_ids)) {
                // If no errors, commit the transaction
                DB::commit();
                return null; // Return empty array indicating success
            } else {
                // If there are errors, rollback the transaction
                DB::rollback();
                return $error_ids; // Return article IDs with errors
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $error_ids; // Return article IDs with errors on exception
        }
        return $error_ids; // Return article IDs with errors if any
    }

    public function processRowSKUlevel($row, $update_column)
    {
        $error_ids = [];
        DB::beginTransaction();
        try {
            foreach ($row as $index => $sku) {
                if ($index === 0) continue; // Skip array index 0

                $data_before = DB::table('product_stocks')
                    ->where('ps_barcode', $sku[0])->first();

                $ps_barcode = $sku[0];
                $new_value = $sku[1];

                $update_data = DB::table('product_stocks')
                    ->where('ps_barcode', $ps_barcode)
                    ->update([
                        $update_column => $new_value,
                        'updated_at' => now() // Update the updated_at timestamp
                    ]);

                if ($update_data === 0) {
                    $error_ids[] = $ps_barcode; // Collect article IDs with errors
                }

                $productLog = new \App\Models\ProductLogs();
                $timestamp = date('Y-m-d H:i:s');
                $productLog->storeProductStockLog(
                    $data_before->id,
                    $data_before->p_id,
                    Auth::id(),
                    ProductLogs::LEVEL_SKU,
                    $update_column,
                    '/mass-update-products',
                    $data_before->$update_column,
                    $new_value,
                    $timestamp
                );
            }

            if (empty($error_ids)) {
                // If no errors, commit the transaction
                DB::commit();
                return null; // Return empty array indicating success
            } else {
                // If there are errors, rollback the transaction
                DB::rollback();
                return $error_ids; // Return article IDs with errors
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $error_ids; // Return article IDs with errors on exception
        }
        return $error_ids; // Return article IDs with errors if any
    }
}
