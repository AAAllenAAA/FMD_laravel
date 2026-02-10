<?php

namespace App\Services;

class ECPayService
{
    // 將金鑰定義為屬性，方便管理
    private $hashKey = '5294y06JbISpM5x9';
    private $hashIV = 'v77hoKGq4kWxNNIS';
    private $merchantID = '2000132';

    /**
     * 取得基本參數
     */
    public function getBasicParams()
    {
        return [
            'MerchantID' => $this->merchantID,
            'MerchantTradeDate' => date('Y/m/d H:i:s'),
            'PaymentType' => 'aio',
            'ChoosePayment' => 'ALL',
            'EncryptType' => 1,
        ];
    }

    /**
     * 生成綠界加密檢查碼
     */
    public function generateCheckMacValue($params)
    {
        unset($params['CheckMacValue']);
        ksort($params);

        $rawString = "HashKey=" . $this->hashKey;
        foreach ($params as $key => $value) {
            $rawString .= "&" . $key . "=" . $value;
        }
        $rawString .= "&HashIV=" . $this->hashIV;

        $text = strtolower(urlencode($rawString));

        // 綠界特有的符號替換
        $text = str_replace(
            ['%2d', '%5f', '%2e', '%21', '%2a', '%28', '%29'],
            ['-', '_', '.', '!', '*', '(', ')'],
            $text
        );

        return strtoupper(hash('sha256', $text));
    }
}