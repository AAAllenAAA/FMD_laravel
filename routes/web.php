<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FmdController;

/*
php artisan serve 網址要改為: http://127.0.0.1:8000/upload
否則就要用: http://localhost/FMD_laravel/public/upload
*/ 

Route::get('/', function () {
    return view('welcome');
});

// 顯示上傳表單
Route::get('/upload', [FmdController::class, 'showUploadForm'])->name('fmd.uploadform');
// 處理上傳
Route::post('/upload', [FmdController::class, 'handleUpload'])->name('fmd.upload');
// search
Route::get('/search', [FmdController::class, 'searchModelName'])->name('fmd.search');
// data (search result data)
Route::get('/data', [FmdController::class,'getFMDdata'])->name('fmd.data');