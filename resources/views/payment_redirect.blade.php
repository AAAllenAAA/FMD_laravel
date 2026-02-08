<!DOCTYPE html>
<html>
<head>
    <title>正在導向至綠界支付...</title>
</head>
<body>
    <p>正在為您導向至支付頁面，請稍候...</p>
    
    <form id="ecpay_form" method="POST" action="https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5">
        @foreach($params as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script>
        // 頁面載入後自動送出表單
        document.getElementById('ecpay_form').submit();
    </script>
</body>
</html>