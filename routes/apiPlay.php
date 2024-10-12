<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AoVivoController;


//Route::get('/schedule',             [AoVivoController::class, 'schedule'])->name('aovivo.schedule'); // transferido para web.php
Route::get('/test',                 [AoVivoController::class, 'test'])->name('aovivo.test');
