<!DOCTYPE html>
<html>
<head>
    <title>FMD 金流測試</title>
</head>
<body>
    <div style="text-align: center; margin-top: 100px;">
        <h2>專案費用結算測試 (NT$ 2,000)</h2>
        <form action="{{ route('payment.go') }}" method="POST">
            @csrf
            <button type="submit" style="padding: 15px 30px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                確認支付，前往綠界
            </button>
        </form>
    </div>
</body>
</html>