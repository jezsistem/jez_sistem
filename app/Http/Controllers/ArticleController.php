<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStock;
use App\Models\ProductLocationSetup;
use App\Models\Product;
use App\Models\ExceptionLocation;

class ArticleController extends Controller
{   
    function fetch(Request $request)
    {
        if($request->get('query'))
        {
            $query = $request->get('query');
            $data = Product::selectRaw("CONCAT(ts_products.id) as pid, CONCAT(p_name,' (',br_name,')') as p_name_brand, CONCAT(p_name) as p_name, CONCAT(br_name) as br_name, CONCAT(ps_qty) as ps_qty")
                    ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
                    ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
                    ->leftJoin('sizes', 'sizes.id', '=', 'product_stocks.sz_id')
                    ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
                    ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                    ->where('pls_qty', '>', 0)
                    ->whereNotIn('pl_code', ['TRIAL','TO01','PJ01','RJK','TI01'])
                    ->where('p_name', 'LIKE', "%{$query}%")
                    ->groupBy('p_name')
                    ->get();
            $output = '<ul class="dropdown-menu form-control" style="display:block; position:relative;">';
            if (!empty($data)) {
                foreach($data as $row) {
                    $output .= '
                    <li><a class="btn btn-sm btn-inventory col-12" data-p_name="'.$row->p_name.'" data-br_name="'.$row->br_name.'" data-id="'.$row->pid.'" id="show_item"><span style="float-left;">'.$row->p_name.' ['.$row->br_name.']</span></a></li>
                    ';
                }
            } else {
                $output .= '<li><a class="btn btn-sm btn-primary">Tidak ditemukan</a></li>';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function checkArticle(Request $request)
    {
        $pid = $request->_pid;
        $p_name = $request->_p_name;
        $br_name = $request->_br_name;
        $color = null;
        $data_item = array();
        $item = Product::select('id', 'p_color')->where('p_name', $p_name)->get();
        if (!empty($item)) {
            foreach ($item as $irow) {
                $sub_item = ProductStock::select('product_stocks.id as ps_id', 'sz_name', 'ps_qty')
                ->join('sizes', 'sizes.id', '=', 'product_stocks.sz_id')->where('p_id', $irow->id)->get();
                if (!empty($sub_item)) {
                    $irow->subitem = $sub_item;
                    foreach ($sub_item as $row) {
                        $item_location = ProductLocationSetup::select('pl_code', 'pl_name', 'pls_qty')
                        ->join('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
                        ->where('pst_id', $row->ps_id)->get();
                        if (!empty($item_location)) {
                            $irow->subonsubitem = $item_location;
                        }
                    }
                }
                array_push($data_item, $irow);
            }
        }
        $data = [
            'p_name' => $p_name,
            'data_item' => $data_item,
            'br_name' => $br_name,
        ];
        return view('auth.article.article', compact('data'));
    }
}
