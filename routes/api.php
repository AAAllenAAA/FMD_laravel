<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FmdController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
| 要使用RESTful API 
| 1. 首先要先改AJAX的URL "url": "/api/fmd/search",
| 2. 然後再改XAMPP的(E:\XAMPP\apache\conf\extra\httpd-vhosts.conf)最後註解拿掉
| 3. 用系統管理員身分打開記事本並開啟舊檔案->C:\Windows\System32\drivers\etc\hosts 把最後註解拿掉(127.0.0.1 laravel.test)
| 4. XAMPP Apache重開
| 5. 網址改用http://laravel.test/, http://laravel.test/upload
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 新增的 API 路由
Route::get('/fmd/search', [FmdController::class, 'getFMDdata']);

// 網址會是 http://laravel.test/api/production/efficiency
Route::get('/production/efficiency', [FmdController::class, 'calculateEfficiency']);