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

class ProductImportBackup implements ToCollection, WithStartRow
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
                $pc_id = null;
                $psc_id = null;
                $pssc_id = null;
                $br_id = null;
                $ps_id = null;
                $pu_id = null;
                $gn_id = null;
                $ss_id = null;
                $mc_id = null;

                $product_category = ProductCategory::where('pc_name', '=', ltrim($r[3]));
                if (!empty($product_category->first()->id)) {
                    $pc_id = $product_category->first()->id;
                    $status += 1;
                } else {
                    $this->rows = -1;
                    $status = -1;
                    $this->error_messages[] = $r[0] . ' Product Category Not Found';
                    break;
                    // dd($product_category);
                }
                $product_sub_category = ProductSubCategory::where('pc_id', $pc_id)->where('psc_name', '=', ltrim($r[4]));
                if (!empty($product_sub_category->first()->id)) {
                    $psc_id = $product_sub_category->first()->id;
                    $status += 1;
                } else {
                    $this->rows = -1;
                    $status = -1;
                    $this->error_messages[] = $r[0] . 'PSC Not Found';
                    break;
                    // dd($product_sub_category);
                }
                $product_sub_sub_category = ProductSubSubCategory::where('psc_id', $psc_id)->where('pssc_name', '=', ltrim($r[5]));
                if (!empty($product_sub_sub_category->first()->id)) {
                    $pssc_id = $product_sub_sub_category->first()->id;
                    $status += 1;
                } else {
                    $this->rows = -1;
                    $status = 'PSSC Not Found';
                    $this->error_messages[] = $r[0] . ' PSSC Not Found';
                    break;
                    // dd($product_sub_sub_category);
                }
                $brand = Brand::where('br_name', '=', ltrim($r[6]));
                if (!empty($brand->first()->id)) {
                    $br_id = $brand->first()->id;
                    $status += 1;
                } else {
                    $this->rows = -1;
                    $status = 'Brand Not Found';
                    $this->error_messages[] = $r[0] . ' Brand Not Found';
                    break;
                    // dd($brand);
                }
                $product_supplier = ProductSupplier::where('ps_name', '=', ltrim($r[7]));
                if (!empty($product_supplier->first()->id)) {
                    $ps_id = $product_supplier->first()->id;
                    $status += 1;
                } else {
                    $this->rows = -1;
                    $status = 'Supplier Not Found';
                    $this->error_messages[] = $r[0] . ' Supplier Not Found';
                    break;
                    // dd($product_supplier);
                }
                $product_unit = ProductUnit::where('pu_name', '=', ltrim($r[8]));
                if (!empty($product_unit->first()->id)) {
                    $pu_id = $product_unit->first()->id;
                    $status += 1;
                } else {
                    $this->rows = -1;
                    $status = 'Product Unit Not Found';
                    $this->error_messages[] = $r[0] . ' Product Unit Not Found';
                    break;
                    // dd($product_unit);
                }
                $gender = Gender::where('gn_name', '=', ltrim($r[9]));
                if (!empty($gender->first()->id)) {
                    $gn_id = $gender->first()->id;
                    $status += 1;
                } else {
                    $this->rows = -1;
                    $status = 'Gender Not Found';
                    $this->error_messages[] = $r[0] . ' Gender Not Found';
                    break;
                    // dd($gender);
                }
                $season = Season::where('ss_name', '=', ltrim($r[10]));
                if (!empty($season->first()->id)) {
                    $ss_id = $season->first()->id;
                    $status += 1;
                } else {
                    $this->rows = -1;
                    $status = 'Seasson Name Not Found';
                    $this->error_messages[] = $r[0] . ' Season Name Not Found';
                    break;
                    // dd($season);
                }
                $main_color = MainColor::where('mc_name', '=', ltrim($r[12]));
                if (!empty($main_color->first()->id)) {
                    $mc_id = $main_color->first()->id;
                    $status += 1;
                } else {
                    $this->rows = -1;
                    $status = 'Main Color Not Found';
                    $this->error_messages[] = $r[0] . ' Main Color Not Found';
                    break;
                    // dd($main_color);
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
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $data_id[] = $p_id;

                if (!empty($p_id)) {
                    $exp = explode('_', ltrim($r[17]));
                    $count = (int)count($exp);

                    for ($i = 0; $i <= $count; $i++) {
                        if (empty($exp[$i])) {
                            continue;
                            $this->rows = -1;
                            $this->error_messages[] = $r[0] . ' Product Stock Not Found';
                            break;
                        }
                        $exp_ps = explode('|', $exp[$i]);

                        $size = Size::where(['sz_schema' => $schema_size, 'sz_name' => $exp_ps[0]]);
                        if (!empty($size->first()->id)) {
                            $sz_id = $size->first()->id;
                        } else {
                            // continue;
                            $this->rows = -1;
                            $this->error_messages[] = $r[0] . ' Size ' . $exp_ps[0] . ' Not Found | ';
                            $this->rows = -1;
                            $status = -1;
                            break;
                        }
                        if (empty($exp_ps[1])) {
                            $barcode = null;
                            $this->error_messages[] = $r[0] . ' Barcode ' . $exp_ps[1] . ' Empty | ';
                            $this->rows = -1;
                            $status = -1;
                            break;
                        } else {
                            $barcode = $exp_ps[1];
                        }
                        if (empty($exp_ps[2])) {
                            $price_tag = null;
                            $this->error_messages[] = $r[0] . ' Price Tag ' . $exp_ps[2] . ' Empty | ';
                            $this->rows = -1;
                            $status = -1;
                            break;
                        } else {
                            $price_tag = $exp_ps[2];
                        }
                        if (empty($exp_ps[3])) {
                            $sell_price = null;
                            $this->error_messages[] = $r[0] . ' Sell Price ' . $exp_ps[3] . ' Empty | ';
                            $this->rows = -1;
                            $status = -1;
                            break;
                        } else {
                            $sell_price = $exp_ps[3];
                        }
                        if (empty($exp_ps[4])) {
                            $purchase_price = null;
                            $this->error_messages[] = $r[0] . ' Purchase Price ' . $exp_ps[4] . ' Empty | ';
                            $this->rows = -1;
                            $status = -1;
                            break;
                        } else {
                            $purchase_price = $exp_ps[4];
                        }

                        // check ps barcode already exists or not,if  exists, return error
                        $ps = ProductStock::where('ps_barcode', '=', $barcode)->exists();

                        if ($ps) {
                            $this->rows = -1;
                            $status = -1;
                            $this->error_messages[] = $r[0] . ' Barcode ' . $barcode . ' Already Exists | ';
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
                            'ps_running_code' => $this->generateRunningCode()
                        ]);
                    }
                } else {
                    $this->rows = -1;
                    $status = -1;
                    $this->error_messages[] = $r[0] . ' Product Not Found';
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
            $this->error_messages[] = $e->getMessage();
            $this->rows = -1;
            DB::rollBack();
            return '400';
        }
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