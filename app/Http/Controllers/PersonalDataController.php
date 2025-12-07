<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use App\Models\WebConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PersonalDataController extends Controller
{
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
        $user = new User();
        $select = ['*'];
        $where = [
            'users.id' => Auth::user()->id
        ];
        $user_data = $user->checkJoinData($select, $where)->first();
        $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;

        $data = [
            'title' => $title,
            'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title ?? 'Personal Data',
            'user' => $user_data,
            'segment' => request()->segment(1),
            'sidebar' => $this->sidebar(),
        ];
        return view('app.user_personal_data.index', compact('data','user_data'));
    }

    public function updateData(Request $request)
    {
        $userId = Auth::user()->id;
        $updateData = [];
        $bucketName = config('filesystems.disks.s3.bucket');
        
        // Get current user data to access old file paths
        $user = User::find($userId);

        // Handle KTP image upload
        if ($request->hasFile('ktp_image')) {
            // Delete old file if exists
            if ($user->u_ktp_image) {
                $oldPath = str_replace($bucketName . '/', '', $user->u_ktp_image);
                Storage::disk('s3')->delete($oldPath);
            }
            
            $ktpFile = $request->file('ktp_image');
            $ktpFileName = $userId . '_' . uniqid() . '.' . $ktpFile->getClientOriginalExtension();
            $ktpPath = $ktpFile->storeAs('personal_data/ktp', $ktpFileName, 's3', 'public');
            $updateData['u_ktp_image'] = $bucketName . '/' . $ktpPath;
        }

        // Handle NPWP image upload
        if ($request->hasFile('npwp_image')) {
            // Delete old file if exists
            if ($user->u_npwp_image) {
                $oldPath = str_replace($bucketName . '/', '', $user->u_npwp_image);
                Storage::disk('s3')->delete($oldPath);
            }
            
            $npwpFile = $request->file('npwp_image');
            $npwpFileName = $userId . '_' . uniqid() . '.' . $npwpFile->getClientOriginalExtension();
            $npwpPath = $npwpFile->storeAs('personal_data/npwp', $npwpFileName, 's3', 'public');
            $updateData['u_npwp_image'] = $bucketName . '/' . $npwpPath;
        }

        // Handle photo upload
        if ($request->hasFile('foto_formal')) {
            // Delete old file if exists
            if ($user->u_photo) {
                $oldPath = str_replace($bucketName . '/', '', $user->u_photo);
                Storage::disk('s3')->delete($oldPath);
            }
            
            $photoFile = $request->file('foto_formal');
            $photoFileName = $userId . '_' . uniqid() . '.' . $photoFile->getClientOriginalExtension();
            $photoPath = $photoFile->storeAs('personal_data/foto', $photoFileName, 's3', 'public');
            $updateData['u_photo'] = $bucketName . '/' . $photoPath;
        }

            // Handle BPJS Kesehatan image upload
        if ($request->hasFile('foto_bpjs_kesehatan')) {
            // Delete old file if exists
            if ($user->u_bpjs_kes_image) {
                $oldPath = str_replace($bucketName . '/', '', $user->u_bpjs_kes_image);
                Storage::disk('s3')->delete($oldPath);
            }
            
            $bpjsKesFile = $request->file('foto_bpjs_kesehatan');
            $bpjsKesFileName = $userId . '_' . uniqid() . '.' . $bpjsKesFile->getClientOriginalExtension();
            $bpjsKesPath = $bpjsKesFile->storeAs('personal_data/bpjs_kes', $bpjsKesFileName, 's3', 'public');
            $updateData['u_bpjs_kes_image'] = $bucketName . '/' . $bpjsKesPath;
        }

        // Handle BPJS TK image upload
        if ($request->hasFile('foto_bpjs_ketenagakerjaan')) {
            // Delete old file if exists
            if ($user->u_bpjs_tk_image) {
                $oldPath = str_replace($bucketName . '/', '', $user->u_bpjs_tk_image);
                Storage::disk('s3')->delete($oldPath);
            }
            
            $bpjsFile = $request->file('foto_bpjs_ketenagakerjaan');
            $bpjsFileName = $userId . '_' . uniqid() . '.' . $bpjsFile->getClientOriginalExtension();
            $bpjsPath = $bpjsFile->storeAs('personal_data/bpjs_tk', $bpjsFileName, 's3', 'public');
            $updateData['u_bpjs_tk_image'] = $bucketName . '/' . $bpjsPath;
        }

        // Add other text fields
        $updateData['u_ktp'] = $request->input('nik');
        $updateData['u_npwp'] = $request->input('npwp');
        $updateData['u_birthday'] = $request->input('tanggal_lahir');
        $updateData['u_address'] = $request->input('alamat_domisili');
        $updateData['u_bpjs_kes_number'] = $request->input('no_bpjs_kesehatan');
        $updateData['u_bank_name'] = $request->input('nama_bank');
        $updateData['u_bank_account_number'] = $request->input('no_rekening');
        $updateData['u_bank_account_holder'] = $request->input('atas_nama_rekening');

        User::where('id', $userId)->update($updateData);

        $this->UserActivity('Updated personal data.');

        return redirect()->back()->with('success', 'Personal data updated successfully.');
    }
}
