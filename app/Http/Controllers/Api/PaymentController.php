<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ECPayService;

class PaymentController extends Controller
{
    protected $ecpayService;

    // 建構子輸入Service
    public function __construct(ECPayService $eCPayService)
    {
        $this->ecpayService = $eCPayService;
    }

    /**
     * 發起金流結帳 (對應 RESTful 的 Store)
     */
    public function goECPay()
    {
        $params = array_merge($this->ecpayService->getBasicParams(), [
            'MerchantTradeNo' => 'ALLEN' . time(),
            'TotalAmount' => 2000,
            'TradeDesc' => 'ProjectPayment',
            'ItemName' => 'ProjectPayment',
            'ReturnURL' => 'https://www.ecpay.com.tw/receive.php',
            'OrderResultURL' => 'http://127.0.0.1:8000/payment/result',
        ]);
        //dd($params);
        
        $params['CheckMacValue'] = $this->ecpayService->generateCheckMacValue($params);

        //dd(view('payment_redirect', ['params' => $params]));

        return view('payment_redirect', ['params' => $params]);
    }

    /**
     * 接收綠界回傳的付款結果 (對應 RESTful 的 Update)
     */
    public function update(Request $request)
    {
        $data = $request->all();

        // 1. 暫存綠界傳過來的 CheckMacValue，然後從陣列中移除
        $receivedCheckMacValue = $data['CheckMacValue'];
        unset($data['CheckMacValue']);

        // 這裡調用你之前的加密 method
        $checkValue = $this->ecpayService->generateCheckMacValue($data);

        // 3. 比對雜湊值與 RtnCode
        if ($receivedCheckMacValue === $checkValue && (int) $data['RtnCode'] === 1) {
            

            // 4. 成功後必須回傳這行字給綠界，不然綠界會一直重複發通知給你
            return "1|OK";
        }

        return "0|Error";
    }
}