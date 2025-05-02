<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v2025\OmnichannelController;

Route::middleware(['auth'])->group(function () {
    Route::get('omnichannel', [OmnichannelController::class, 'index']);
});
