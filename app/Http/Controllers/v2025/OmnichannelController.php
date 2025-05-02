<?php

namespace App\Http\Controllers\v2025;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\WebConfig;
use App\Models\User;

class OmnichannelController extends Controller
{

  public function index()
  {
      validateAccess();
      $user = new User;
      $select = ['*'];
      $where = [
          'users.id' => Auth::user()->id
      ];
      $user_data = $user->checkJoinData($select, $where)->first();
      $title = WebConfig::select('config_value')->where('config_name', 'app_title')->get()->first()->config_value;
      $path = "
      <li class='breadcrumb-item'>
          <a href='' class='text-muted'>Omnichannel</a>
      </li>";
      $data = [
          'title' => $title,
          'subtitle' => DB::table('menu_accesses')->where('ma_slug', '=', request()->segment(1))->first()->ma_title,
          'sidebar' => sidebar(),
          'path' => $path,
          'user' => $user_data,
          'segment' => request()->segment(1),
      ];
      return view('app.v2025.omnichannel.omnichannel', compact('data'));
  }

}