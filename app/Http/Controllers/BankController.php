<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WebConfig;
use App\Models\User;
use App\Models\Bank;
use App\Models\UserActivity;
use Maatwebsite\Excel\Facades\Excel;
use Image;
use File;

class BankController extends Controller
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
    
    protected function UserActivity($activity)
    {
        UserActivity::create([
            'user_id' => Auth::user()->id,
            'ua_description' => $activity,
            'created_at' => date('Y-m-d H:i:s')
        ]);
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
            <a href='' class='text-muted'>Bank</a>
        </li>";
        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
            'sidebar' => $this->sidebar(),
            'path' => $path,
            'user' => $user_data,
            'segment' => request()->segment(1),
        ];
        return view('app.bank.bank', compact('data'));
    }

    public function getDatatables(Request $request)
    {
        if(request()->ajax()) {
            return datatables()->of(Bank::select('id', 'bank_image', 'bank_name', 'bank_account_name', 'bank_number'))
            ->editColumn('bank_image_show', function($data){
                if (empty($data->bank_image)) {
                    return '<img src="'.asset('upload/image/no_image.png').'"/>';
                } else {
                    return '<a href="'.asset('api/bank/100').'/'.$data->bank_image.'" target="_blank"><img src="'.asset('api/bank/100').'/'.$data->bank_image.'" /></a>';
                }
            })
            ->rawColumns(['bank_image_show'])
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('bank_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function storeData(Request $request)
    {
        $bank = new Bank;
        $mode = $request->input('_mode');
        $id = $request->input('_id');

        $data = [
            'bank_name' => strtoupper($request->input('bank_name')),
            'bank_account_name' => $request->input('bank_account_name'),
            'bank_number' => $request->input('bank_number'),
        ];

        if ($request->hasFile('bank_image')) {
            $request->validate([
                'bank_image' => 'required|file|mimes:jpg,jpeg,png',
            ]);
            $image = $request->file('bank_image');
            $input['fileName'] = time().'.'.$image->extension();

            $destinationPath = public_path('/api/bank/100');
            $img = Image::make($image->path());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$input['fileName']);

            if ($mode=='edit') {
                if(File::exists(public_path('api/bank/100/'. $request->_image))){
                    File::delete(public_path('api/bank/100/'. $request->_image));
                }
            }

            $data = array_merge($data,['bank_image' => $input['fileName']]);
        }

        $save = $bank->storeData($mode, $id, $data);
        if ($save) {
            if ($mode == 'add') {
                $this->UserActivity('menambah data bank '.strtoupper($request->input('bank_name')));
            } else {
                $this->UserActivity('mengubah data bank '.strtoupper($request->input('bank_name')));
            }
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function deleteData(Request $request)
    {
        $bank = new Bank;
        $id = $request->input('_id');
        $item_name = Bank::select('bank_name')->where('id', $id)->get()->first()->bank_name;
        $delete = Bank::where('id', '=', $id)->delete();
        if ($delete) {
            $this->UserActivity('menghapus data bank '.$item_name);
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }

    public function checkExistsBank(Request $request)
    {
        $check = Bank::where(['bank_name' => strtoupper($request->_bank_name)])->exists();
        if ($check) {
            $r['status'] = '200';
        } else {
            $r['status'] = '400';
        }
        return json_encode($r);
    }
}
