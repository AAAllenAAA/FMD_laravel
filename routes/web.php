<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FmdController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Http\Request;

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

// Test RESTful API and ECPay 2026 / 02 / 08
// 顯示結帳按鈕的頁面
Route::get('/checkout', function () {
    return view('checkout');
});

// 處理按下按鈕後，產生資料並「自動導向」綠界
Route::post('/payment/go', [PaymentController::class, 'goECPay'])->name('payment.go');

// 接收付款結果跳轉的頁面
Route::post('/payment/result', function (Request $request) {
    // 綠界會把付款結果 POST 到這裡，你可以 dd 看看
    // dd($request->all()); 
    
    $rtnCode = $request->input('RtnCode');
    
    if ($rtnCode == 1) {
        return "<h1>付款成功！感謝您的購買</h1>";
    } else {
        return "<h1>付款失敗，錯誤代碼：" . $rtnCode . "</h1>";
    }
});