<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductLocationSetup;
use App\Models\ProductCategory;
use App\Models\ExceptionLocation;
use Image;
use File;

class WebArticleController extends Controller
{
    protected function validateAccess()
    {
        $validate = DB::table('user_menu_accesses')
        ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
            'u_id' => Auth::user()->id,
            'ma_slug' => request()->segment(1)
        ])->exists();
        if (!$validate) {
            dd("Anda tidak memiliki akses ke menu ini, hubungi Administrator");
        }
    }

    protected function sidebar()
    {
        $ma_id = DB::table('user_menu_accesses')->select('ma_id')
        ->where('u_id', Auth::user()->id)->get();
        $ma_id_arr = array();
        if (!empty($ma_id)) {
            foreach ($ma_id as $row) {
                array_push($ma_id_arr, $row->ma_id);
            }
        }

        $sidebar = array();
        $mt = DB::table('menu_titles')->orderBy('mt_sort')->get();
        if (!empty($mt->first())) {
            foreach ($mt as $row) {
                $ma = DB::table('menu_accesses')
                ->where('mt_id', '=', $row->id)
                ->whereIn('id', $ma_id_arr)
                ->orderBy('ma_sort')->get();
                if (!empty($ma->first())) {
                    $row->ma = $ma;
                    array_push($sidebar, $row);
                }
            }
        }
        return $sidebar;
    }
    
    public function index()
    {
        $this->validateAccess();
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'pc_id' => ProductCategory::where('pc_delete', '!=', '1')->orderByDesc('id')->pluck('pc_name', 'id'),
        ];
        return view('app.web_article.web_article', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        $exception = ExceptionLocation::select('pl_code')
        ->leftJoin('product_locations', 'product_locations.id', '=', 'exception_locations.pl_id')->get()->toArray();
        if(request()->ajax()) {
            return datatables()->of(Product::selectRaw('ts_products.id as pid, article_id ,br_name, p_name, p_color, p_main_image, p_image, p_size_chart, p_slug, p_description, p_video, p_weight, sum(ts_product_location_setups.pls_qty) as stok')
            ->leftJoin('brands', 'brands.id', '=', 'products.br_id')
            ->leftJoin('product_stocks', 'product_stocks.p_id', '=', 'products.id')
            ->leftJoin('product_location_setups', 'product_location_setups.pst_id', '=', 'product_stocks.id')
            ->leftJoin('product_locations', 'product_locations.id', '=', 'product_location_setups.pl_id')
            ->where('p_delete', '!=', '1')
            ->whereNotIn('product_locations.pl_code', $exception)
//            ->havingRaw('sum(ts_product_location_setups.pls_qty) >= 0')
            ->groupBy('products.id')
            ->orderBy('products.id', 'desc'))
            ->editColumn('p_main_image_show', function($data){
                if (!empty($data->p_main_image)) {
                    $image = "<img data-chart_image='".$data->p_size_chart."' data-main_image='".$data->p_main_image."' data-image='".$data->p_image."' data-name='".$data->br_name." ".$data->p_name." ".$data->p_color."' id='p_image_edit' data-id='".$data->pid."'  style='width:100px;' src='".asset('api/product/300')."/".$data->p_main_image."' alt='main_image'/>";
                } else {
                    $image = "<img data-chart_image='".$data->p_size_chart."' data-main_image='".$data->p_main_image."' data-image='".$data->p_image."' data-name='".$data->br_name." ".$data->p_name." ".$data->p_color."' id='p_image_edit' data-id='".$data->pid."'  src='".asset('api/noimage.png')."' alt='main_image'/>";
                }
                return $image;
            })
            ->editColumn('p_size_chart_show', function($data){
                if (!empty($data->p_size_chart)) {
                    $image = "<img data-chart_image='".$data->p_size_chart."' data-main_image='".$data->p_main_image."' data-image='".$data->p_image."' data-name='".$data->br_name." ".$data->p_name." ".$data->p_color."' id='p_image_edit' data-id='".$data->pid."'  style='width:100px;' src='".asset('api/product/size_chart')."/".$data->p_size_chart."' alt='chart_image'/>";
                } else {
                    $image = "<img data-chart_image='".$data->p_size_chart."' data-main_image='".$data->p_main_image."' data-image='".$data->p_image."' data-name='".$data->br_name." ".$data->p_name." ".$data->p_color."' id='p_image_edit' data-id='".$data->pid."'  src='".asset('api/noimage.png')."' alt='chart_image'/>";
                }
                return $image;
            })
            ->editColumn('p_description_show', function($data){
                if (!empty($data->p_description) AND !empty($data->p_video)) {
                    return "<a data-video='".htmlspecialchars($data->p_video)."' data-description='".htmlspecialchars($data->p_description)."' data-name='".$data->br_name." ".$data->p_name." ".$data->p_color."' id='p_description_edit' data-id='".$data->pid."'  class='btn-sm btn-primary text-white'>Detail</a>";
                } else if (!empty($data->p_description) AND empty($data->p_video)) {
                    return "<a data-video='".htmlspecialchars($data->p_video)."' data-description='".htmlspecialchars($data->p_description)."' data-name='".$data->br_name." ".$data->p_name." ".$data->p_color."' id='p_description_edit' data-id='".$data->pid."'  class='btn-sm btn-warning text-white'>Detail</a>";
                } else {
                    return "<a data-video='".htmlspecialchars($data->p_video)."' data-description='".htmlspecialchars($data->p_description)."' data-name='".$data->br_name." ".$data->p_name." ".$data->p_color."' id='p_description_edit' data-id='".$data->pid."'  class='btn-sm btn-danger text-white'>Detail</a>";
                }
            })
            ->editColumn('p_weight_show', function($data){
                return "<input class='form-control col-12' type='number' id='p_weight' value='".$data->p_weight."' data-id='".$data->pid."' placeholder='Berat(gr)'/>";
            })
            ->editColumn('p_slug_show', function($data){
                return "
                <div class='row'>
                    <input id='p_slug_input' data-id='".$data->pid."' class='form-control col-11 p_slug_input_".$data->pid."' type='text' value='".$data->p_slug."' autocomplete='off'/>
                    <i id='generate_slug_btn' data-slug='".strtolower(str_replace('/', '-', str_replace(' ', '-', $data->br_name)))."-".strtolower(str_replace('/', '-', str_replace(' ', '-', $data->p_name)))."-".strtolower(str_replace('/', '-', str_replace(' ', '-', $data->p_color)))."' data-id='".$data->pid."' class='fa fa-edit btn-sm btn-primary text-white'></i>
                </div>
                ";
            })
            ->editColumn('action', function($data){
                $ecommerce_url = DB::table('web_configs')->select('config_value')
                ->where('config_name', 'ecommerce_url')->first()->config_value;

                return "<a href='".$ecommerce_url."/".$data->p_slug."' target='_blank' class='btn-sm btn-success'><i class='fa fa-eye text-white'></i></a>";
            })
            ->rawColumns(['p_image_show', 'p_main_image_show', 'p_description_show', 'p_slug_show', 'p_size_chart_show', 'p_weight_show', 'action'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhereRaw('CONCAT(article_id," ",br_name," ", p_name," ", p_color) LIKE ?', "%$search%")
                        ->orWhereRaw('article_id LIKE ?', "%$search%");
                    });
                }
                if (!empty($request->get('pc_id'))) {
                    $instance->where(function($w) use($request){
                        $pc_id = $request->get('pc_id');
                        $w->where('pc_id', '=', $pc_id);
                    });
                }
                if ($request->get('img_filter') != '') {
                    $instance->where(function($w) use($request){
                        $img = $request->get('img_filter');
                        if ($img == '1') {
                          $w->whereNotNull('products.p_main_image');
                        } else if ($img == '0') {
                          $w->whereNull('products.p_main_image');
                        }
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function saveWeight(Request $request)
    {
        $id = $request->post('id');
        $weight = $request->post('weight');
        $update = Product::where('id', '=', $id)->update([
          'p_weight' => $weight
        ]);
        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function generateSlug(Request $request)
    {
        $pid = $request->input('pid');
        $slug = strtolower(str_replace(' ', '-', ltrim($request->input('slug'))));
        $check = Product::where('p_slug', '=', $slug)->exists();
        if ($check) {
            $r['status'] = '400';
        } else {
            Product::where('id', '=', $pid)->update([
                'p_slug' => $slug
            ]);
            $r['status'] = '200';
        }
        return json_encode($r);
    }

    public function saveDescription(Request $request)
    {
        $pid = $request->post('pid');
        $description = $request->post('description');
        $video = $request->post('video');
        $update = Product::where('id', '=', $pid)->update([
            'p_description' => $description,
            'p_video' => $video,
        ]);
        if (!empty($update)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveMainImage(Request $request)
    {
        $pid = $request->post('pid');
        if ($request->hasFile('p_main_image')) {
            $request->validate([
                'p_main_image' => 'required|file|mimes:jpg,jpeg,png',
            ]);
            $image = $request->file('p_main_image');
            $input['fileName'] = time().'.'.$image->extension();

            $destinationPath = public_path('/api/product/300');
            $img = Image::make($image->path());
            $img->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$input['fileName']);

            $destinationPath = public_path('/api/product/600');
            $img = Image::make($image->path());
            $img->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$input['fileName']);
            $p_main_image = $input['fileName'];
            Product::where('id', '=', $pid)->update([
                'p_main_image' => $p_main_image
            ]);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function saveImage(Request $request)
    {
        $pid = $request->post('pid');
        $p_main_image = '';
        $p_image = '';
        $p_size_chart = '';
        $main_image = null;
        $detail_image = null;
        $chart_image = null;
        if ($request->hasFile('p_main_image')) {
            $request->validate([
                'p_main_image' => 'required|file|mimes:jpg,jpeg,png',
            ]);
            $image_m = $request->file('p_main_image');
            $input['fileName_m'] = (time()+111).'.'.$image_m->extension();

            $destinationPath_m = public_path('/api/product/300');
            $img_m = Image::make($image_m->path());
            $img_m->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath_m.'/'.$input['fileName_m']);

            $destinationPath_m = public_path('/api/product/600');
            $img_m = Image::make($image_m->path());
            $img_m->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath_m.'/'.$input['fileName_m']);
            $p_main_image = $input['fileName_m'];
            $main_image = Product::where('id', '=', $pid)->update([
                'p_main_image' => $p_main_image
            ]);
        }

        if ($request->hasFile('p_size_chart')) {
            $request->validate([
                'p_size_chart' => 'required|file|mimes:jpg,jpeg,png',
            ]);
            $image_m = $request->file('p_size_chart');
            $input['fileName_m'] = (time()+222).'.'.$image_m->extension();

            $destinationPath_m = public_path('/api/product/size_chart');
            $img_m = Image::make($image_m->path());
            $img_m->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath_m.'/'.$input['fileName_m']);
            $p_size_chart = $input['fileName_m'];
            $chart_image = Product::where('id', '=', $pid)->update([
                'p_size_chart' => $p_size_chart
            ]);
        }

        if($request->post('TotalImages') > 0)
        {
            for ($x = 0; $x < $request->post('TotalImages'); $x++)
            {
                if ($request->hasFile('p_images'.$x))
                {
                    $request->validate([
                        'p_images'.$x => 'required|file|mimes:jpg,jpeg,png',
                    ]);
                    $image = $request->file('p_images'.$x);
                    $input['fileName'] = (time()+$x).'.'.$image->extension();

                    $destinationPath = public_path('/api/product/300');
                    $img = Image::make($image->path());
                    $img->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($destinationPath.'/'.$input['fileName']);

                    $destinationPath = public_path('/api/product/600');
                    $img = Image::make($image->path());
                    $img->resize(600, 600, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($destinationPath.'/'.$input['fileName']);

                    if ($x == ($request->post('TotalImages')-1)) {
                        $p_image .= $input['fileName'];
                    } else {
                        $p_image .= $input['fileName'].'|';
                    }
                }
            }
            $detail_image = Product::where('id', '=', $pid)->update([
                'p_image' => $p_image
            ]);
        }
        if (!empty($main_image) || !empty($detail_image) || !empty($chart_image)) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteMainImage(Request $request)
    {
        $pid = $request->post('pid');
        $image = $request->post('image');
        if(File::exists(public_path('api/product/300/'. $image))){
            File::delete(public_path('api/product/300/'. $image));
        }
        if(File::exists(public_path('api/product/600/'. $image))){
            File::delete(public_path('api/product/600/'. $image));
        }
        $product = Product::where('id', '=', $pid)->update([
            'p_main_image' => null
        ]);
        if (!empty($product)) {
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteChartImage(Request $request)
    {
        $pid = $request->post('pid');
        $image = $request->post('image');
        if(File::exists(public_path('api/product/size_chart/'. $image))){
            File::delete(public_path('api/product/size_chart/'. $image));
        }
        $product = Product::where('id', '=', $pid)->update([
            'p_size_chart' => null
        ]);
        if (!empty($product)) {
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteImage(Request $request)
    {
        $pid = $request->post('pid');
        $image = $request->post('image');
        $exp = explode('|', $image);
        $total = count($exp);
        $product = null;
        if ($total > 0) {
          for ($i=0; $i< $total; $i++) {
            if(File::exists(public_path('api/product/300/'. $exp[$i]))){
                File::delete(public_path('api/product/300/'. $exp[$i]));
            }
            if(File::exists(public_path('api/product/600/'. $exp[$i]))){
                File::delete(public_path('api/product/600/'. $exp[$i]));
            }
          }
          $product = Product::where('id', '=', $pid)->update([
              'p_image' => null
          ]);
        }
        if (!empty($product)) {
          $r['status'] = '200';
        } else {
          $r['status'] = '400';
        }
        return json_encode($r);
    }
}
