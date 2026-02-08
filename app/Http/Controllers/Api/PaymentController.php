<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * 發起金流結帳 (對應 RESTful 的 Store)
     */
    public function goECPay()
    {
        // 1. 綠界測試環境設定值 (這些以後可以放在 .env 檔)
        $merchantID = '2000132';
        $hashKey = '5294y06JbISpM5x9';
        $hashIV = 'v77hoKGq4kWxNNIS';

        // 2. 準備訂單參數
        $params = [
            'MerchantID' => $merchantID,
            'MerchantTradeNo' => 'ALLEN' . time(),
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'PaymentType' => 'aio',
            'TotalAmount' => 2000,
            'TradeDesc' => 'ProjectPayment',
            'ItemName' => 'ProjectPayment',
            'ReturnURL' => 'https://www.ecpay.com.tw/receive.php', // 背景通知網址 (必填，但可先填官方範例)
            'OrderResultURL' => 'http://127.0.0.1:8000/payment/result', // <--- 加上這一行，付款完跳回來的地方
            'ChoosePayment' => 'ALL',
            'EncryptType' => 1,
        ];
        //dd($params);

        // 3. 計算 CheckMacValue (加密檢查碼)
        $checkMacValue = $this->generateCheckMacValue($params, $hashKey, $hashIV);

        // 4. 重點：一定要把算好的值塞回原本的陣列！
        $params['CheckMacValue'] = $checkMacValue;

        //dd(view('payment_redirect', ['params' => $params]));

        // 5. 回傳到自動跳轉頁面
        return view('payment_redirect', ['params' => $params]);
    }

    /**
     * 綠界加密演算法
     */
    private function generateCheckMacValue($params, $hashKey, $hashIV)
    {
        // 1. 確保沒有 CheckMacValue 本身
        unset($params['CheckMacValue']);

        // 2. 字典排序
        ksort($params);

        // 3. 組合字串
        $rawString = "HashKey=" . $hashKey;
        foreach ($params as $key => $value) {
            $rawString .= "&" . $key . "=" . $value;
        }
        $rawString .= "&HashIV=" . $hashIV;

        // 4. URL Encode
        $text = urlencode($rawString);

        // 5. 轉小寫
        $text = strtolower($text);

        // 6. 這是綠界官方指定的「符號替換表」，一個都不能少！
        // 我們要把 PHP urlencode 產生的某些編碼換回原始符號
        $text = str_replace(
            ['%2d', '%5f', '%2e', '%21', '%2a', '%28', '%29'],
            ['-', '_', '.', '!', '*', '(', ')'],
            $text
        );

        // 7. 額外保險：如果 URL 裡面有中文或特殊字元，確保編碼與綠界一致
        // 雖然你現在用全英文，但加上這層保護更穩
        return strtoupper(hash('sha256', $text));
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

        // 2. 使用跟發起交易時「一模一樣」的邏輯算一遍 CheckMacValue
        $hashKey = '5294y06JbISpM5x9';
        $hashIV = 'v77hoKGq4kWxNNIS';

        // 這裡調用你之前的加密 method
        $checkValue = $this->generateCheckMacValue($data, $hashKey, $hashIV);

        // 3. 比對雜湊值與 RtnCode
        if ($receivedCheckMacValue === $checkValue && (int) $data['RtnCode'] === 1) {
            

            // 4. 成功後必須回傳這行字給綠界，不然綠界會一直重複發通知給你
            return "1|OK";
        }

        return "0|Error";
    }
}