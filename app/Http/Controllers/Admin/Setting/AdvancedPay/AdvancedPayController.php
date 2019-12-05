<?php

namespace App\Http\Controllers\Admin\Setting\AdvancedPay;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdvancedPayController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except("logout");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data_check = $this->check_permission("SYS039", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $data_json = $this->call_api_by_parameter("webGetAdvancedPay", ['UserID' => $user_id]);
//        $data_group = $this->call_api_by_parameter("webGetMTPickup", ["UserID" => $user_id]);

        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        }

        $data_encode = json_decode($data_json);
//        $group_decode = json_decode($data_group);

        $data['id'] = $data_encode->id;
        $data['list'] = $data_encode->data;
        $data['user_id'] = $user_id;
//        $data['mt_id'] = $group_decode->id;
//        $data['mt_list'] = $group_decode->data;

        $data['add'] = CheckPemission($this->check_permission("SYS039", 'add'));
        $data['edit'] = CheckPemission($this->check_permission("SYS039", 'edit'));
        $data['delete'] = CheckPemission($this->check_permission("SYS039", 'delete'));

        return view('pages.backend.setting.advancedpay.show', compact("data"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {

        $parmsadd = [
            "UserID" => $request->user_id,
            "ReasonID" => $request->purpose,
            "MTPickupID" => $request->mtpickup,
        ];

        $name = "webAddAdvancedPay";

        if ($request->id) {

            $name = "webEditAdvancedPay";

            $parmsadd = [
                "ID" => $request->id,
                "UserID" => $request->user_id,
                "ReasonID" => $request->purpose,
                "MTPickupID" => $request->mtpickup,
                "Status" => $request->status,
            ];
        }

        $call_api = $this->call_api_by_parameter($name, $parmsadd);

        if ($call_api) {

            $data_encode = json_decode($call_api);
            if ($data_encode->id) {
                return response()->json([
                    'data' => $data_encode->data[0],
                ]);
            }

            return response()->json([
                'error' => $data_encode->message
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection."
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $data_api = $this->call_api_by_parameter("webDeleteAdvancedPay", array("UserID" => $request->user_id, "ID" => $request->id));

        if ($data_api) {
            $decode = json_decode($data_api);
            if ($decode->id) {
                return response()->json([
                    "message" => "Data Advanced Pay Deleted"
                ]);
            }

            return response()->json([
                'error' => $decode->message
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function for import data fleet
    public function import(Request $request)
    {

        set_time_limit(1800);

        $user_id = Session::get("ID");

        $validator = Validator::make(
            [
                'file' => $request->file_excel,
                'extension' => strtolower($request->file_excel->getClientOriginalExtension()),
            ],
            [
                'file' => 'required',
                'extension' => 'required|in:xlsx,xls,csv',
            ]
        );
        // process the form
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ]);
        }

        try {

            $i = 0;
            $j = 0;
            $k = 0;

            $load_file = \Excel::load($request->file_excel, 'UTF-8')->get();
            $reader = $load_file->toArray();

            foreach ($reader as $row) {

                $parmsadd = [
                    "UserID" => $user_id,
                    "Purpose" => trim($row['purpose']),
                    "MTPickup" => trim($row['mtpickup']),
                ];

                $call_api = $this->call_api_by_parameter("webAddAdvancedPayByImport", $parmsadd);
                $decode = json_decode($call_api);

                if ($decode->id) {
                    $j++;
                } else {
                    $k++;
                }

                $i++;
            }

            return response()->json([
                'message' => "Your file upload completed. Total data is " . $j . " rows. Inserted: " . $j . " rows, Not insert: " . $k . " rows"
            ]);


        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function import_matrix(Request $request)
    {
        set_time_limit(1800);

        $user_id = Session::get("ID");

        $validator = Validator::make(
            [
                'file' => $request->file_excel,
                'extension' => strtolower($request->file_excel->getClientOriginalExtension()),
            ],
            [
                'file' => 'required',
                'extension' => 'required|in:xlsx,xls,csv',
            ]
        );
        // process the form
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ]);
        }

        try {

            $i = 0;
            $k = 0;
            $m = 0;
            $n = 0;
            $l = 0;

            $load_file = \Excel::load($request->file_excel, 'UTF-8')->get();
            $reader = $load_file->toArray();
            $headerRow = $load_file[0]->toArray();


            foreach ($reader as $row) {
                $j = 0;
                if ($i > 0) {
                    foreach ($headerRow as $key => $value) {
                        if ($j > 0) {

                            if ($row[$key] != null) {
                                echo $j . "=Destinaiton:" . $row['purpose'] . ",Purpose:" . $value . ", Paytrip:" . $row[$key] . "<br/>";

//                                $parmsadd = [
//                                    "UserID" => $user_id,
//                                    "Purpose" => trim($row['purpose']),
//                                    "Purpose" => trim($value),
//                                    "PayTrip" => trim($row[$key]),
//                                ];
//
                                $parmsadd = [
                                    "UserID" => $user_id,
                                    "Purpose" => trim($row['purpose']),
                                    "MTPickup" => trim($value),
                                ];
                                dd($parmsadd);
//
//                                $call_api = $this->call_api_by_parameter("webAddAdvancedPayByImport", $parmsadd);
//                                $decode = json_decode($call_api);
//
//                                if ($decode->id) {
//                                    $n++;
//                                } else {
//                                    $m++;
//                                }
                            }

                            $l++;

                        }
                        $j++;
                    }
                    $k++;
                }
                $i++;
            }
            dd();
            return response()->json([
                'message' => "Your file upload completed. Total data is " . $l . " rows. Inserted: " . $n . " rows, Not insert: " . $m . " rows"
            ]);


        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    //this function for download example purpose excel
    public function download()
    {

        $cus_list = array();
        $cus_list[] = ['Purpose', 'MTPickup'];
        $cus_list[] = ['2-EXP,MT To FTY', 'TL_20'];
        $cus_list[] = ['2-EXP,MT To FTY', 'UN_20'];
        $cus_list[] = ['2-EXP,MT To FTY', 'NTS_20'];

        return \Excel::create('advancedpay_example', function ($excel) use ($cus_list) {

            $excel->sheet('sheet name', function ($sheet) use ($cus_list) {

                $sheet->fromArray($cus_list, null, 'A1', false, false);
                $sheet->cells('A1:B1', function ($cells) {

                    $cells->setAlignment('center');
                    $cells->setFontWeight("bold");
                    $cells->setFontColor('#000000');
                    $cells->setFontSize('12');
                });
            });
        })->download('xlsx');
    }

    public function download_matrix()
    {

        $cus_list = array();
        $cus_list[] = ['Purpose', '1', '2', '3', '4', '5', '6'];
        $cus_list[] = ['Purpose', 'TL_20', 'UN_20', 'NTS_20', 'GPG_20', 'HJN_20', 'CDT_20'];
        $cus_list[] = ['2-EXP,MT To FTY', '0', '15', '15', '15', '15', '15'];
        $cus_list[] = ['6-ENV-THOMAS', '0', '15', '15', '15', '15', '15'];
        $cus_list[] = ['5-ENV-DAMCO', '0', '15', '15', '15', '15', '15'];

        return \Excel::create('advancedpay_example', function ($excel) use ($cus_list) {

            $excel->sheet('sheet name', function ($sheet) use ($cus_list) {

                $sheet->fromArray($cus_list, null, 'A1', false, false);
                $sheet->cells('A1:G1', function ($cells) {

                    $cells->setAlignment('center');
                    $cells->setFontWeight("bold");
                    $cells->setFontColor('#000000');
                    $cells->setFontSize('12');
                });
            });
        })->download('xlsx');
    }

    public function save_matrix(Request $request)
    {
        $user_id = Session::get("ID");
        $n = 0;
        $m = 0;
        $l = 0;
        for ($i = 0; $i < count($request->purpose_add); $i++) {
            for ($j = 0; $j < count($request->mtpickup_add); $j++) {
                $parmsadd = [
                    "UserID" => $user_id,
                    "ReasonID" => $request->purpose_add[$i],
                    "MTPickupID" => $request->mtpickup_add[$j],
                ];
                $call_api = $this->call_api_by_parameter("webAddAdvancedPay", $parmsadd);
                $decode = json_decode($call_api);

                if ($decode->id) {
                    $n++;
                } else {
                    $m++;
                }

                $l++;
            }
        }

        return response()->json([
            'error' => 0,
            'message' => "Your data save completed. Total data is " . $l . " rows. Inserted: " . $n . " rows, Not insert: " . $m . " rows"
        ]);
    }
}
