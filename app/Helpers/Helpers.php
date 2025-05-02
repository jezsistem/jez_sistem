<?php

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

if (!function_exists('sCrypt')) {
  function sCrypt($str)
  {
    return Crypt::encryptString($str);
  }
}

if (!function_exists('sDecrypt')) {
  function sDecrypt($str)
  {
    return Crypt::decryptString($str);
  }
}
