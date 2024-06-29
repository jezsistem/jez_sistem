<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Brand;
use App\Models\MainColor;
use App\Models\ProductSupplier;
use App\Models\ProductUnit;
use App\Models\ProductStock;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;
use App\Models\Gender;
use App\Models\Season;
use App\Models\Size;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;

class ProductImport implements ToCollection, WithStartRow
{
    private $sameArticleId = array();
    private $error_messages = [];
    private $rows = 0;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function startRow(): int
    {
        return 2;
    }

    public function generateRunningCode()
    {
        $check = ProductStock::select('ps_running_code')->orderByDesc('ps_running_code')->limit(1)->get()->first();
        if (!empty($check)) {
            $current_running_code = $check->ps_running_code;
            $next_running_code = $current_running_code + 1;
            $running_length = strlen($next_running_code);
            $new_running_code = '';
            if ($running_length == 1) {
                $new_running_code = '000000000000' . $next_running_code;
            } else if ($running_length == 2) {
                $new_running_code = '00000000000' . $next_running_code;
            } else if ($running_length == 3) {
                $new_running_code = '0000000000' . $next_running_code;
            } else if ($running_length == 4) {
                $new_running_code = '000000000' . $next_running_code;
            } else if ($running_length == 5) {
                $new_running_code = '00000000' . $next_running_code;
            } else if ($running_length == 6) {
                $new_running_code = '0000000' . $next_running_code;
            } else if ($running_length == 7) {
                $new_running_code = '000000' . $next_running_code;
            } else if ($running_length == 8) {
                $new_running_code = '00000' . $next_running_code;
            } else if ($running_length == 9) {
                $new_running_code = '0000' . $next_running_code;
            } else if ($running_length == 10) {
                $new_running_code = '000' . $next_running_code;
            } else if ($running_length == 11) {
                $new_running_code = '00' . $next_running_code;
            } else if ($running_length == 12) {
                $new_running_code = '0' . $next_running_code;
            } else if ($running_length == 13) {
                $new_running_code = $next_running_code;
            }
        } else {
            $new_running_code = '0000000000001';
        }

        if ($this->runningCodeExists($new_running_code)) {
            return $this->generateRunningCode();
        }
        return $new_running_code;
    }

    public function runningCodeExists($number)
    {
        return ProductStock::where(['ps_running_code' => $number])->exists();
    }

    public function collection(Collection $row)
    {
        try {
            DB::beginTransaction();
            ++$this->rows;
            $data_id = array();
            $status = 0;

            // Fetch all necessary data in advance
            $productCategories = ProductCategory::all()->keyBy('pc_name');
            $productSubCategories = ProductSubCategory::all()->groupBy('pc_id')->map(function ($group) {
                return $group->keyBy('psc_name');
            });
            $productSubSubCategories = ProductSubSubCategory::all()->groupBy('psc_id')->map(function ($group) {
                return $group->keyBy('pssc_name');
            });
            $brands = Brand::all()->keyBy('br_name');
            $productSuppliers = ProductSupplier::all()->keyBy('ps_name');
            $productUnits = ProductUnit::all()->keyBy('pu_name');
            $genders = Gender::all()->keyBy('gn_name');
            $seasons = Season::all()->keyBy('ss_name');
            $mainColors = MainColor::all()->keyBy('mc_name');
            $sizes = Size::all()->groupBy('sz_schema')->map(function ($group) {
                return $group->keyBy('sz_name');
            });

            foreach ($row as $r) {
                if ($r[0] == null) {
                    return null;
                }

                if ($this->articeIdChecker($r[0])) {
                    $this->setSameArticleId($r[0]);
                    DB::table('product_stocks')->whereIn('p_id', $data_id)->delete();
                    DB::table('products')->whereIn('id', $data_id)->delete();
                    break;
                }

                $article_id = null;
                $schema_size = $r[1];

                $pc_id = $this->getId($productCategories, ltrim($r[3]), 'Product Category');
                $psc_id = $this->getId($productSubCategories[$pc_id] ?? null, ltrim($r[4]), 'PSC');
                $pssc_id = $this->getId($productSubSubCategories[$psc_id] ?? null, ltrim($r[5]), 'PSSC');
                $br_id = $this->getId($brands, ltrim($r[6]), 'Brand');
                $ps_id = $this->getId($productSuppliers, ltrim($r[7]), 'Supplier');
                $pu_id = $this->getId($productUnits, ltrim($r[8]), 'Product Unit');
                $gn_id = $this->getId($genders, ltrim($r[9]), 'Gender');
                $ss_id = $this->getId($seasons, ltrim($r[10]), 'Season Name');
                $mc_id = $this->getId($mainColors, ltrim($r[12]), 'Main Color');

                if ($this->rows == -1) {
                    $status = -1;
                    break;
                }

                $p_id = DB::table('products')->insertGetId([
                    'br_id' => $br_id,
                    'pc_id' => $pc_id,
                    'psc_id' => $psc_id,
                    'pssc_id' => $pssc_id,
                    'mc_id' => $mc_id,
                    'ps_id' => $ps_id,
                    'pu_id' => $pu_id,
                    'gn_id' => $gn_id,
                    'ss_id' => $ss_id,
                    'article_id' => ltrim($r[0]),
                    'schema_size' => ltrim($r[1]),
                    'p_name' => ltrim($r[2]),
                    'p_aging' => ltrim($r[11]),
                    'p_color' => ltrim($r[13]),
                    'p_price_tag' => ltrim($r[14]),
                    'p_purchase_price' => ltrim($r[15]),
                    'p_sell_price' => ltrim($r[16]),
                    'p_delete' => '0',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $data_id[] = $p_id;

                if (!empty($p_id)) {
                    $exp = explode('_', ltrim($r[17]));
                    foreach ($exp as $psData) {
                        if (empty($psData)) {
                            $this->setError($r[0], 'Product Stock Not Found');
                            $status = -1;
                            break;
                        }

                        $exp_ps = explode('|', $psData);
                        if (count($exp_ps) < 5) {
                            $this->setError($r[0], 'Incomplete Product Stock Data');
                            $status = -1;
                            break;
                        }

                        $sz_id = $this->getId($sizes[$schema_size] ?? null, $exp_ps[0], 'Size', false);
                        if (empty($sz_id)) {
                            $this->setError($r[0], 'Size ' . $exp_ps[0] . ' Not Found');
                            $status = -1;
                            break;
                        }

                        $barcode = $this->getBarcode($exp_ps[1], $r[0]);
                        $price_tag = $this->getPriceTag($exp_ps[2], $r[0]);
                        $sell_price = $this->getSellPrice($exp_ps[3], $r[0]);
                        $purchase_price = $this->getPurchasePrice($exp_ps[4], $r[0]);

                        if ($this->rows == -1) {
                            $status = -1;
                            break;
                        }

                        if (ProductStock::where('ps_barcode', $barcode)->exists()) {
                            $this->setError($r[0], 'Barcode ' . $barcode . ' Already Exists');
                            $status = -1;
                            break;
                        }

                        ProductStock::create([
                            'p_id' => $p_id,
                            'sz_id' => $sz_id,
                            'ps_qty' => '0',
                            'ps_barcode' => $barcode,
                            'ps_price_tag' => $price_tag,
                            'ps_sell_price' => $sell_price,
                            'ps_purchase_price' => $purchase_price,
                            'ps_running_code' => $this->generateRunningCode(),
                        ]);
                    }
                } else {
                    $this->setError($r[0], 'Product Not Found');
                    $status = -1;
                    break;
                }
            }

            if ($status >= 0) {
                DB::commit();
                return '2000';
            } else {
                DB::table('product_stocks')->whereIn('p_id', $data_id)->delete();
                DB::table('products')->whereIn('id', $data_id)->delete();
                $this->rows = -1;
                DB::rollBack();
                return '400';
            }
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), 'Error');
            $this->rows = -1;
            DB::rollBack();
            return '400';
        }
    }

    private function getId($collection, $name, $errorMessage, $required = true)
    {
        if ($collection && $collection->has($name)) {
            return $collection->get($name)->id;
        }

        if ($required) {
            $this->rows = -1;
            $this->error_messages[] = $name . ' ' . $errorMessage . ' Not Found';
        }

        return null;
    }

    private function setError($row, $message)
    {
        $this->rows = -1;
        $this->error_messages[] = $row . ' ' . $message;
    }

    private function getBarcode($value, $row)
    {
        if (empty($value)) {
            $this->setError($row, 'Barcode Empty');
            return null;
        }
        return $value;
    }

    private function getPriceTag($value, $row)
    {
        if (empty($value)) {
            $this->setError($row, 'Price Tag Empty');
            return null;
        }
        return $value;
    }

    private function getSellPrice($value, $row)
    {
        if (empty($value)) {
            $this->setError($row, 'Sell Price Empty');
            return null;
        }
        return $value;
    }

    private function getPurchasePrice($value, $row)
    {
        if (empty($value)) {
            $this->setError($row, 'Purchase Price Empty');
            return null;
        }
        return $value;
    }


    public function getRowCount(): int
    {
        return $this->rows;
    }

    // create to save same array value
    private function setSameArticleId($article_id): void
    {
        $this->sameArticleId[] = $article_id;
    }

    public function getSameArticleId(): array
    {
        return $this->sameArticleId;
    }

    public function getErrorMessages(): array
    {
        return $this->error_messages;
    }

    private function articeIdChecker($article_id): bool
    {
        $article = Product::where('article_id', '=', ltrim($article_id))->exists();
        if ($article) {
            return true;
        } else {
            return false;
        }
    }
}
