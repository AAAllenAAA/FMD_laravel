<!-- test Git 2025/09/15 CI/CD test-->
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>FMD Upload & Query</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1c1c1e;
            color: #f1f1f1;
        }

        .card {
            background-color: #2c2c2e;
            border-radius: 12px;
            border: 1px solid #444;
        }

        .card-header {
            border-bottom: 1px solid #444;
        }

        .form-control,
        .form-control-file {
            background-color: #fff;
            color: #000;
        }

        .btn-primary,
        .btn-info {
            border-radius: 8px;
        }

        .alert {
            border-radius: 8px;
        }

        .custom-alert-wrapper {
            max-width: 500px;
            margin: 20px auto;
        }

        .custom-alert {
            font-size: 1.1rem;
            text-align: center;
            transition: opacity 0.5s ease;
        }
    </style>
</head>

<body>
    <br>
    <div class="text-center mb-5">
        <h1 style="font-weight: 600; font-size: 2.5rem; color: #ffffff;">
            <i class="fas fa-database text-info"></i> FMD 資料上傳與查詢系統
        </h1>
        <p style="color: #ccc;">提供 Excel 檔案上傳與機種FMD資料查詢功能</p>
    </div>


    <div class="container mt-5">
        <!-- 成功/錯誤訊息 -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="關閉">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <script>
                // 成功訊息 5 秒後自動關閉
                setTimeout(() => {
                    const alertEl = document.querySelector('.alert-success.custom-alert');
                    if (alertEl) {
                        $(alertEl).alert('close');
                    }
                }, 5000);
            </script>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="關閉">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- 上傳表單 -->
        <div class="row">
            <div class="col-md-6 mb-4 d-flex">
                <div class="card border-info shadow w-100 h-100">
                    <div class="card-header bg-info text-white">
                        <h4><i class="fas fa-upload"></i> 上傳 FMD Excel</h4>
                    </div>
                    <div class="card-body">
                        <form id="uploadForm" action="{{ route('fmd.upload') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="modelInput">輸入機種：</label>
                                <input type="text" name="model" id="modelInput" class="form-control" placeholder="請輸入機種"
                                    autocomplete="off" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="xlsxFile">選擇 Excel 檔案：</label>
                                <input type="file" name="excelFile" id="xlsxFile" class="form-control-file"
                                    accept=".xlsx,.xls" required>
                            </div>
                            <button id="uploadBtn" type="submit" class="btn btn-light">
                                <i class="fas fa-file-upload"></i> 上傳
                            </button>
                        </form>
                    </div>
                </div>
            </div>


            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const uploadForm = document.getElementById('uploadForm');
                    const uploadBtn = document.getElementById('uploadBtn');
                    const modelInput = document.getElementById("modelInput");
                    const fileInput = document.getElementById("xlsxFile");

                    fileInput.style.pointerEvents = 'none';
                    fileInput.style.opacity = 0.5;

                    // 當輸入機種時，啟用檔案欄位
                    modelInput.addEventListener("input", function () {
                        if (modelInput.value.trim() !== "") {
                            fileInput.style.pointerEvents = 'auto';
                            fileInput.style.opacity = 1;
                        } else {
                            fileInput.value = ""; // 清除檔案
                            fileInput.style.pointerEvents = 'none';
                            fileInput.style.opacity = 0.5;
                        }
                    });

                    uploadForm.addEventListener('submit', function () {
                        uploadBtn.disabled = true;
                        uploadBtn.classList.add('btn-secondary');
                        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 上傳中...';
                    });
                });
            </script>


            <!-- 查詢區 -->
            <div class="col-md-6 mb-4 d-flex">
                <div class="card border-info shadow w-100 h-100">
                    <div class="card-header bg-info text-white">
                        <h4><i class="fas fa-search"></i> 查詢紀錄</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('fmd.search') }}" method="GET">
                            <div class="form-group">
                                <label for="keyword">機種查詢：</label>
                                <input type="text" name="keyword" id="keyword" class="form-control" placeholder="機種"
                                    autocomplete="off" required>
                            </div>
                            <button type="submit" class="btn btn-light mt-3"><i class="fas fa-search"></i> 查詢</button>
                        </form>
                    </div>
                </div>
            </div>



        </div>
    </div>


</body>

</html>