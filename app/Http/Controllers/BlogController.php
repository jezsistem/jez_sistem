<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WebConfig;
use App\Models\User;
use Image;
use File;

class BlogController extends Controller
{
    protected function validateAccess($slug = null)
    {
        $ma_slug = $slug ?? request()->segment(1);
        $validate = DB::table('user_menu_accesses')
        ->leftJoin('menu_accesses', 'menu_accesses.id', '=', 'user_menu_accesses.ma_id')->where([
            'u_id' => Auth::user()->id,
            'ma_slug' => $ma_slug
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
        $path = "
        <li class='breadcrumb-item'>
            <a href='' class='text-muted'>Website</a>
        </li>
        <li class='breadcrumb-item'>
            <a href='' class='text-muted'>Blog</a>
        </li>";
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'path' => $path,
            'user' => $user_data,
            'segment' => request()->segment(1),
            'bc_id' => DB::table('blog_categories')->selectRaw('ts_blog_categories.id as id, bc_name')
            ->orderBy('bc_name')->pluck('bc_name', 'id'),
        ];
        return view('app.blog.blog', compact('data'));
    }

    public function getDetailDatatables(Request $request)
    {
        $ecommerce_url = DB::table('web_configs')->select('config_value')
        ->where('config_name', 'ecommerce_url')->first()->config_value;

        if(request()->ajax()) {
            return datatables()->of(DB::table('blog_contents')->select('blog_contents.id as id', 'bc_id', 'bct_title', 'bct_content', 'bct_image', 'bct_slug', 'bct_keywords', 'bct_views')
            ->leftJoin('blog_categories', 'blog_categories.id', '=', 'blog_contents.bc_id')
            ->where('blog_contents.bc_id', '=', $request->get('bc_id')))
            ->editColumn('bct_image_show', function($w) {
              return "<img style='width:100px;' src='".url('/')."/api/blog/300/".$w->bct_image."'/>";
            })
            ->editColumn('action', function($w) use ($ecommerce_url) {
              return "<a href='".$ecommerce_url."/".$w->bct_slug."' target='_blank' class='btn-sm btn-primary'><i class='fa fa-eye'></i></a>";
            })
            ->escapeColumns('bct_content')
            ->rawColumns(['action', 'bct_image_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('bct_title', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function getData(Request $request)
    {
        $id = $request->input('_id');
        $data = DB::table('blog_categories')->select('bc_name')
        ->where('id', '=', $id)->get()->first();
        if (!empty($data)) {
            $r['bc_name'] = $data->bc_name;
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function storeData(Request $request)
    {
        $mode = $request->input('_bc_mode');
        $id = $request->input('_bc_id');

        $data = [
            'bc_name' => $request->input('bc_name'),
            'bc_slug' => \Str::slug($request->input('bc_name')),
        ];

        if ($mode == 'add') {
            $data = array_merge($data, ['created_at' => date('Y-m-d H:i:s')]);
            $save = DB::table('blog_categories')->insert($data);
        } else {
            $data = array_merge($data, ['updated_at' => date('Y-m-d H:i:s')]);
            $save = DB::table('blog_categories')->where('id', '=', $id)->update($data);
        }

        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }

        return json_encode($r);
    }

    public function storeDetailData(Request $request)
    {
        $mode = $request->input('_bcc_mode');
        $id = $request->input('_bcc_id');

        $data = [
            'bct_title' => $request->input('bcc_title'),
            'bct_slug' => \Str::slug($request->input('bcc_title')),
            'bct_content' => $request->input('bcc_content'),
            'bct_keywords' => $request->input('bcc_keywords'),
            'bct_views' => '0',
            'bc_id' => $request->input('bc_id'),
            'u_id' => Auth::user()->id,
        ];

        if ($request->hasFile('bcc_image')) {
            $request->validate([
                'bcc_image' => 'required|file|mimes:jpg,jpeg,png',
            ]);
            $image = $request->file('bcc_image');
            $input['fileName'] = time().'.'.$image->extension();

            $destinationPath = public_path('/api/blog/300');
            $img = Image::make($image->path());
            $img->fit(300, 300, function ($constraint) {
            })->save($destinationPath.'/'.$input['fileName']);

            $destinationPath = public_path('/api/blog/600');
            $img = Image::make($image->path());
            $img->resize(600, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$input['fileName']);

            if ($mode=='edit') {
                if(File::exists(public_path('api/blog/300/'. $request->_bcc_image))){
                    File::delete(public_path('api/blog/300/'. $request->_bcc_image));
                }
                if(File::exists(public_path('api/blog/600/'. $request->_bcc_image))){
                    File::delete(public_path('api/blog/600/'. $request->_bcc_image));
                }
            }
            $data = array_merge($data,['bct_image' => $input['fileName']]);
        }

        if ($mode == 'add') {
            $data = array_merge($data, ['created_at' => date('Y-m-d H:i:s')]);
            $save = DB::table('blog_contents')->insert($data);
        } else {
            $data = array_merge($data, ['updated_at' => date('Y-m-d H:i:s')]);
            $save = DB::table('blog_contents')->where('id', '=', $id)->update($data);
        }

        if ($save) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }

        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $id = $request->input('_id');
        $delete = DB::table('blog_contents')->where('bc_id', '=', $id)->delete();
        $delete = DB::table('blog_categories')->where('id', '=', $id)->delete();
        if ($delete) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteDetailData(Request $request)
    {
        $id = $request->input('_id');
        $delete = DB::table('blog_contents')->where('id', '=', $id)->delete();
        if ($delete) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function indexUpdated()
    {
        $this->validateAccess('blog');
        $user = new User;
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', 'blog')->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'user' => $user_data,
            'segment' => request()->segment(1),
            'bc_id' => DB::table('blog_categories')->selectRaw('ts_blog_categories.id as id, bc_name')
            ->orderBy('bc_name')->pluck('bc_name', 'id'),
        ];
        return view('app.updated_blog.blog', compact('data'));
    }

    public function getDatatablesForSimple(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 25);
            $search = $request->get('search', '');
            $bc_id = $request->get('bc_id', '');

            $ecommerce_url = DB::table('web_configs')->select('config_value')
                ->where('config_name', 'ecommerce_url')->first()->config_value ?? '';

            $query = DB::table('blog_contents')
                ->select('blog_contents.id as id', 'bc_id', 'bct_title', 'bct_content', 'bct_image', 'bct_slug', 'bct_keywords', 'bct_views')
                ->leftJoin('blog_categories', 'blog_categories.id', '=', 'blog_contents.bc_id');

            if (!empty($bc_id)) {
                $query->where('blog_contents.bc_id', '=', $bc_id);
            }

            if (!empty($search)) {
                $query->where(function ($w) use ($search) {
                    $w->where('bct_title', 'LIKE', "%$search%");
                });
            }

            $total = $query->count();
            $totalPages = ceil($total / $perPage);

            $query->orderBy('blog_contents.id', 'desc');
            $query->skip(($page - 1) * $perPage)->take($perPage);

            $results = $query->get();

            $data = [];
            $no = ($page - 1) * $perPage + 1;
            foreach ($results as $row) {
                $bct_image_url = !empty($row->bct_image) ? asset('api/blog/300/' . $row->bct_image) : asset('api/noimage.png');
                $data[] = [
                    'no' => $no++,
                    'id' => $row->id,
                    'bc_id' => $row->bc_id,
                    'bct_title' => $row->bct_title ?? '-',
                    'bct_content' => $row->bct_content ?? '',
                    'bct_image' => $row->bct_image,
                    'bct_image_url' => $bct_image_url,
                    'bct_slug' => $row->bct_slug ?? '-',
                    'bct_keywords' => $row->bct_keywords ?? '-',
                    'bct_views' => $row->bct_views ?? '0',
                    'ecommerce_url' => $ecommerce_url,
                ];
            }

            return response()->json([
                'data' => $data,
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => (int) $page,
                'per_page' => (int) $perPage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage(),
                'data' => [],
                'total' => 0,
                'total_pages' => 0,
                'current_page' => 1,
                'per_page' => 25
            ], 500);
        }
    }
}
