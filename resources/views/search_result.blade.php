@php
    $key = trim(request('keyword', ''));
@endphp
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>FMD 查詢結果</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- ExcelJS & FileSaver -->
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <style>
        body {
            background-color: #1c1c1e;
            color: #f1f1f1;
        }

        table.dataTable {
            background-color: #2c2c2e;
            color: #fff;
        }

        table.dataTable thead {
            background-color: #333;
            color: #fff;
        }

        #datalist th,
        #datalist td {
            border-right: 1px solid #888;
            border-bottom: 1px solid #555;
        }

        #datalist th:last-child,
        #datalist td:last-child {
            border-right: none;
        }
    </style>
</head>

<body>
    <div class="container mb-3">
        <div class="d-flex justify-content-between align-items-start"
            style="background-color: #1c1c1e; color: #bbb; font-size: 0.9rem; padding: 1rem;">
            <div>
                @if ($key === '')
                    <div class="alert alert-warning py-2 px-3 mb-2" style="font-size: 0.9rem;">⚠️ 請提供機種關鍵字。</div>
                @else
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-light mb-2">返回上頁</a>
                    🔍 機種：<strong>{{ $key }}</strong>
                @endif
            </div>
            <div class="d-flex flex-column align-items-end">
                <button class="btn btn-sm btn-outline-light" onclick="exportTableToExcel()">匯出</button>
            </div>
        </div>
    </div>

    <table id="datalist" class="text-center display" style="width:100%">
        <thead>
            <tr>
                <th class='text-center'>Manufacturer</th>
                <th class='text-center'>Unitech Manufacturer Part Number</th>
                <th class='text-center'>MPN Weight</th>
                <th class='text-center'>MPN weight unit of measure</th>
                <th class='text-center'>MPN EU RoHS Compliance Status</th>
                <th class='text-center'>MPN EU RoHS Exemption</th>
                <th class='text-center'>Subitem name/mfg part no</th>
                <th class='text-center'>Homogeneous Material Name</th>
                <th class='text-center'>Homogeneous Material Weight</th>
                <th class='text-center'>Material Weight UOM</th>
                <th class='text-center'>Substance Name</th>
                <th class='text-center'>CAS#</th>
                <th class='text-center'>Substance Weight</th>
                <th class='text-center'>Substance Weight Unit of Measure</th>
                <th class='text-center'>PPM</th>
                <th class='text-center'>Exemption for substance</th>
            </tr>
        </thead>
    </table>

    <!-- jQuery + DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            const dynamicScrollY = window.innerHeight - 250 + "px";
            $('#datalist').DataTable({
                "lengthChange": false,
                "scrollX": true,
                "scrollY": dynamicScrollY,
                "scrollCollapse": true,
                "paging": false,
                "processing": false,
                "bFilter": true,
                "info": false,
                "zeroRecords": true,
                "autoWidth": true,
                "stripeClasses": [],
                "ajax": {
                    "url": "{{ route('fmd.data') }}", // 這裡是Laravel API Route
                    "type": "GET",
                    "data": {
                        key: "{{ $key }}"
                    },
                    "dataType": 'json',
                    "error": function () {
                        alert('尚無資料');
                    }
                },
                "columns": [
                    { data: "manu_name" },             // Manufacturer
                    { data: "manu_partnum" },          // Part Number
                    { data: "mpn_weight" },            // MPN Weight
                    { data: "mpn_weight_UOM" },        // MPN Weight UOM
                    { data: "mpn_EU_RoHS" },           // MPN EU RoHS
                    { data: "mpn_RoHS_exemption" },    // MPN RoHS Exemption
                    { data: "subitem_name" },          // Subitem Name
                    { data: "homo_material_name" },    // Homogeneous Material Name
                    { data: "homo_material_weight" },  // Homogeneous Material Weight
                    { data: "homo_material_weight_UOM" }, // Homogeneous Material Weight UOM
                    { data: "substance_name" },        // Substance Name
                    { data: "sub_cas" },               // CAS
                    { data: "sub_weight" },            // Substance Weight
                    { data: "sub_weight_UOM" },        // Substance Weight UOM
                    { data: "ppm" },                   // PPM
                    { data: "sub_exemption"}
                ]
            });
        });

        async function exportTableToExcel() {
            const confirmExport = confirm("確定要匯出目前檔案嗎?");
            if (!confirmExport) return;

            const table = $('#datalist').DataTable();
            const data = table.rows({ search: 'applied' }).data().toArray();
            if (data.length === 0) {
                alert("目前沒有可匯出的資料");
                return;
            }

            const headers = [];
            $('#datalist thead th').each(function () {
                headers.push($(this).text().trim());
            });

            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet("查詢結果");
            worksheet.addRow(headers);
            const titleRow = worksheet.getRow(1);
            titleRow.eachCell((cell) => {
                cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: '1E90FF' } };
                cell.alignment = { vertical: 'middle', horizontal: 'center' };
            });

            data.forEach(row => {
                worksheet.addRow(Object.values(row));
            });

            worksheet.columns.forEach(column => {
                let maxLength = 10;
                column.eachCell({ includeEmpty: true }, cell => {
                    const val = cell.value ? cell.value.toString() : '';
                    maxLength = Math.max(maxLength, val.length);
                });
                column.width = maxLength + 8;
            });

            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            saveAs(blob, "FMD_{{ urlencode($key) }}.xlsx");
        }
    </script>
</body>

</html>