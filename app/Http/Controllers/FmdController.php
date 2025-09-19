<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use App\Models\mpnData;
use App\Models\Homogeneous;
use App\Models\SubstanceData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;

class FmdController extends Controller
{
    // 顯示上傳表單
    public function showUploadForm()
    {
        return view('upload');
    }

    /*
    // 處理上傳資料
    public function handleUpload(Request $request)
    {
        // 驗證輸入
        $request->validate([
            'model' => 'required|string',
            'excelFile' => 'required|file|mimes:xlsx,xls',
        ]);

        $model = $request->input('model');
        $file = $request->file('excelFile');
        //dump($model);

        // 建立 uploads 資料夾（storage/app/uploads）
        //$destinationPath = storage_path('app/uploads');
        $destinationPath = 'E:/FMD_data/uploads';
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedOriginalName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);
        $timestamp = date('Ymd_Hi');
        $newFileName = "FMD_{$timestamp}_{$sanitizedOriginalName}.{$file->getClientOriginalExtension()}";

        // 移動檔案
        $file->move($destinationPath, $newFileName);
        $fullPath = $destinationPath . '/' . $newFileName;

        // 解析 Excel - Substances-CAS
        if ($xlsx = SimpleXLSX::parse($fullPath)) {
            $sheetIndex = array_search('Substances-CAS', $xlsx->sheetNames());
            if ($sheetIndex === false) {
                return back()->with('error', '找不到 Substances-CAS 工作表');
            }

            $rows = $xlsx->rows($sheetIndex);
            if (count($rows) <= 1) {
                return back()->with('error', 'Substances-CAS 無資料可匯入');
            }

            array_shift($rows); // 移除表頭

            // 物件導向 也可使用原生SQL or Query Builder
            foreach ($rows as $row) {
                DB::table('substance_cas')->updateOrInsert(
                    ['sub_name' => $row[0] ?? ''],
                    [
                        'cas_no' => $row[1] ?? '',
                        'description' => $row[2] ?? ''
                    ]
                );
            }

        } else {
            return back()->with('error', 'Substances-CAS Excel 解析失敗: ' . SimpleXLSX::parseError());
        }

        // 解析 Excel - Exemptions
        if ($xlsx = SimpleXLSX::parse($fullPath)) {
            $sheetIndex = array_search('Exemptions', $xlsx->sheetNames());
            if ($sheetIndex === false) {
                return back()->with('error', '找不到 Exemptions 工作表');
            }

            $rows = $xlsx->rows($sheetIndex);
            if (count($rows) <= 1) {
                return back()->with('error', 'Exemptions 無資料可匯入');
            }

            array_shift($rows); // 移除表頭

            foreach ($rows as $row) {
                DB::table('exemptions')->updateOrInsert(
                    ['exemption_num' => (string) ($row[0] ?? '')],
                    [
                        'description' => $row[1] ?? ''
                    ]
                );
            }

        } else {
            return back()->with('error', 'Exemptions Excel 解析失敗: ' . SimpleXLSX::parseError());
        }

        return back()->with('success', '上傳完成');
    }
    */


    public function handleUpload(Request $request)
    {
        // 驗證輸入
        $request->validate([
            'model' => 'required|string',
            'excelFile' => 'required|file|mimes:xlsx,xls',
        ]);

        $model = $request->input('model');
        $file = $request->file('excelFile');
        //dump($model);

        // 建立 uploads 資料夾（storage/app/uploads）
        $destinationPath = 'E:/FMD_data/uploads';
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $sanitizedOriginalName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);
        $timestamp = date('Ymd_Hi');
        $newFileName = "FMD_{$timestamp}_{$sanitizedOriginalName}.{$file->getClientOriginalExtension()}";

        // 移動檔案
        $file->move($destinationPath, $newFileName);
        $fullPath = $destinationPath . '/' . $newFileName;

        // 解析example資料
        if ($xlsx = SimpleXLSX::parse($fullPath)) {

            $rows = $xlsx->rows(0);

            // 確認行數大於1
            if (count($rows) <= 1) {
                return back()->with('error', 'Excel 無資料可匯入');
            }

            // 移除標題
            array_shift($rows);

            // 解析資料 Insert新的資料
            DB::transaction(function () use ($rows, $model) {

                foreach ($rows as $row) {

                    // Step 1 : 新增 manufacturer
                    $manufacturer = Manufacturer::create([
                        'manu_name' => $row[0],
                        'manu_PartNumber' => $row[1],
                        'manu_model' => $model,

                    ]);

                    // 拿到 manufacturer的manu_id
                    $manuid = $manufacturer->manu_id;

                    // Step 2 : 用$manuid去新增其他table的資料
                    mpnData::create([
                        'manuId' => $manuid,
                        'mpn_weight' => $row[2],
                        'status_RoHS' => $row[4] ?? '',
                        'exemptions_RoHS' => $row[5] ?? '',
                    ]);

                    Homogeneous::create([
                        'homo_MaterialName' => $row[7],
                        'homo_MaterialWeight' => $row[8],
                        'subitem_name' => $row[6],
                        'manuId' => $manuid,
                    ]);

                    SubstanceData::create([
                        'sub_name' => $row[10],
                        'sub_CAS' => $row[11],
                        'sub_Weight' => $row[12],
                        'ppm' => $row[14] ?? '',
                        'sub_exemption' => $row[15] ?? '',
                        'manuId' => $manuid,
                    ]);

                }

            });

        } else {
            return back()->with('error', 'Excel FMD data解析失敗: ' . SimpleXLSX::parseError());
        }

        return back()->with('success', '上傳完成');
    }



    // search FMD 報表
    public function searchModelName(Request $request)
    {
        $keyword = $request->input('keyword');

        return view('search_result', ['keyword' => $keyword]);

    }

    // FMD data from database
    public function getFMDdata(Request $request)
    {
        $key = $request->query('key');
        // 資料庫尚未建立 先用預設資料
        // SQL語法之後會比較複雜一點
        //$data[] = ['test1' => $key, 'test2' => '範例資料A', 'test3' => 'A', 'test4' => 'A', 'test5' => 'A', 'test6' => 'A', 'test7' => 'A', 'test8' => 'A', 'test9' => 'A', 'test10' => 'A', 'test11' => 'A', 'test12' => 'A', 'test13' => 'A', 'test14' => 'A', 'test15' => 'A'];
        //$data[] = ['test1' => $key, 'test2' => '範例資料B', 'test3' => 'B', 'test4' => 'B', 'test5' => 'B', 'test6' => 'B', 'test7' => 'B', 'test8' => 'B', 'test9' => 'B', 'test10' => 'B', 'test11' => 'B', 'test12' => 'B', 'test13' => 'B', 'test14' => 'B', 'test15' => 'B'];
        //$data[] = ['test1' => $key, 'test2' => '範例資料C', 'test3' => 'C', 'test4' => 'C', 'test5' => 'C', 'test6' => 'C', 'test7' => 'C', 'test8' => 'C', 'test9' => 'C', 'test10' => 'C', 'test11' => 'C', 'test12' => 'C', 'test13' => 'C', 'test14' => 'C', 'test15' => 'C'];

        // 找出符合機種關鍵字的manu
        $manufacturers = Manufacturer::where('manu_model', 'like', "%{$key}%")->get();

        $data = [];

        foreach ($manufacturers as $manu) {
            $mpnItem = mpnData::where('manuId', $manu->manu_id)->first();
            $homoItem = Homogeneous::where('manuId', $manu->manu_id)->first();
            $subItem = SubstanceData::where('manuId', $manu->manu_id)->first();

            $data[] = [
                'manu_name' => $manu->manu_name,
                'manu_partnum' => $manu->manu_PartNumber,
                'mpn_weight' => $mpnItem->mpn_weight ?? '',
                'mpn_weight_UOM' => 'mg',
                'mpn_EU_RoHS' => $mpnItem->status_RoHS ?? '',
                'mpn_RoHS_exemption' => $mpnItem->exemptions_RoHS ?? '',
                'subitem_name' => $homoItem->subitem_name ?? '',
                'homo_material_name' => $homoItem->homo_MaterialName ?? '',
                'homo_material_weight' => $homoItem->homo_MaterWeight ?? '',
                'homo_material_weight_UOM' => 'mg',
                'substance_name' => $subItem->sub_name ?? '',
                'sub_cas' => $subItem->sub_CAS ?? '',
                'sub_weight' => $subItem->sub_Weight ?? '',
                'sub_weight_UOM' => 'mg',
                'ppm' => $subItem->ppm ?? '',
                'sub_exemption' => $subItem->sub_exemption ?? '',
            ];
        }

        return response()->json([
            "data" => $data   // DataTables 需要 data 屬性
        ]);
    }

}
