<?php

namespace App\Http\Controllers\Admin\Setting\PayTrip;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PayTripController extends Controller
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

        $data_check = $this->check_permission("SYS038", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $data_json = $this->call_api_by_parameter("webGetPayTrip", ['UserID' => $user_id]);

        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        }

        $data_encode = json_decode($data_json);

        $data['id'] = $data_encode->id;
        $data['list'] = $data_encode->data;
        $data['user_id'] = $user_id;

        $data['add'] = CheckPemission($this->check_permission("SYS038", 'add'));
        $data['edit'] = CheckPemission($this->check_permission("SYS038", 'edit'));
        $data['delete'] = CheckPemission($this->check_permission("SYS038", 'delete'));

        return view('pages.backend.setting.paytrip.show', compact("data"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {

        if ($request->id) {

            $name = "webEditPayTrip";

            $parmsadd = [
                "PurposeID" => $request->purpose,
                "DestinationID" => $request->destination,
                "UserID" => $request->user_id,
                "PayTrip" => $request->pay_amount,
                "ID" => $request->id,
                "Status" => $request->status,
            ];
        } else {

            $parmsadd = [
                "PurposeID" => $request->purpose,
                "DestinationID" => $request->destination,
                "UserID" => $request->user_id,
                "PayTrip" => $request->pay_amount,
            ];

            $name = "webAddPayTrip";
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
        $data_api = $this->call_api_by_parameter("webDeletePayTrip", array("UserID" => $request->user_id, "ID" => $request->id));

        if ($data_api) {
            $decode = json_decode($data_api);
            if ($decode->id) {
                return response()->json([
                    "message" => "Data Pay Trip Deleted"
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
                    "Destination" => trim($row['destination_code']),
                    "Purpose" => trim($row['purpose']),
                    "PayTrip" => trim($row['pay_amount']),
                ];

                $call_api = $this->call_api_by_parameter("webAddPayTripByImport", $parmsadd);
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
//        dd($user_id);

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

//                            if ($row[$key] != null) {
////                                echo $j . "=Destinaiton:" . $row['destination'] . ",Purpose:" . $value . ", Paytrip:" . $row[$key] . "<br/>";
//                                $parmsadd = [
//                                    "UserID" => $user_id,
//                                    "Destination" => trim($row['destination']),
//                                    "Purpose" => trim($value),
//                                    "PayTrip" => trim($row[$key]),
//                                ];
//
//                                $call_api = $this->call_api_by_parameter("webAddPayTripByImport", $parmsadd);
//                                $decode = json_decode($call_api);
//
//                                if ($decode->id) {
//                                    $n++;
//                                } else {
//                                    $m++;
//                                }
//
//                            }

                            $parmsadd = [
                                "UserID" => $user_id,
                                "Destination" => trim($row['destination']),
                                "Purpose" => trim($value),
                                "PayTrip" => trim($row[$key]),
                            ];

                            $call_api = $this->call_api_by_parameter("webAddPayTripByImport", $parmsadd);
                            $decode = json_decode($call_api);

                            if ($decode->id) {
                                $n++;
                            } else {
                                $m++;
                            }

                            $l++;

                        }
                        $j++;
                    }
                    $k++;
                }
                $i++;
            }
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
        $cus_list[] = ['Destination Code', 'Purpose', 'Pay Amount'];
        $cus_list[] = ['PP-ទៅមកផែភ្នំពេញ (Exp, MT​ & ទៅទទេរ)', 'EXPORT-TOLL', '1000'];
        $cus_list[] = ['PP-ទៅមកផែភ្នំពេញ (Exp, MT​ & ទៅទទេរ)', 'EXPORT-P PORT', '2000'];
        $cus_list[] = ['PP-ទៅមកផែភ្នំពេញ (Exp, MT​ & ទៅទទេរ)', 'MT to-P PORT', '3000'];

        return \Excel::create('paytrip_example', function ($excel) use ($cus_list) {

            $excel->sheet('sheet name', function ($sheet) use ($cus_list) {

                $sheet->fromArray($cus_list, null, 'A1', false, false);
                $sheet->cells('A1:C1', function ($cells) {

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
        $cus_list[] = ['Destination', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39'];
        $cus_list[] = ['Destination', '1-EXPORT-TOLL', '8-EXPORT-P PORT', '9-MT to-P PORT', '10-ទៅទទេរ -P PORT', '24-EXPORT-BAVET', '25-MT to-BAVET', '26-ទៅទទេរ -BAVET', '16-EXPORT-SHV', '17-MT to-SHV', '18-ទៅទទេរ -SHV', '32-EXPORT-POIPET', '33-MT to-POIPET', '34-ទៅទទេរ -POIPET', '7-ផ្សេងៗ', '5-ENV-DAMCO', '6-ENV-THOMAS', '14-ទំលាក់ ​MT (IMP-PP)', '30-ទំលាក់ ​MT (IMP-BV)', '22-ទំលាក់ ​MT  (IMP-SHV)', '38-ទំលាក់ ​MT (IMP-PPT)', '2-EXP,MT To FTY', '3-Exp,Laden pickup to TL', '4-ទៅសារេទូ (Export)', '15-ទៅសារេទូ (IMP-PP)', '31-ទៅសារេទូ   (IMP-BV)', '23-ទៅសារេទូ   (IMP-SHV)', '39-ទៅសារេទូ (IMP-PPT)', '13-អូស MT (IMP-PP)', '29-អូស MT (IMP-BV)', '21-អូស MT (IMP-SHV)', '37-អូស MT (IMP-PPT)', '11-FROM P PORT', '27-FROM-BAVET', '19-FROM- SHV', '35-FROM-POIPET', '12-Delivery (IMP-PP)', '28-Delivery (IMP-BV)', '20-Delivery (IMP-SHV)', '36-Delivery (IMP-PPT)'];
        $cus_list[] = ['Export - ​Toll', '5000', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0'];
        $cus_list[] = ['PNH-ទៅមកផែភ្នំពេញ (EXPORT)', '0', '40000', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0'];
        $cus_list[] = ['PNH-ទៅមកផែភ្នំពេញ (MT_20 / 40)', '0', '0', '5000', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0'];

        return \Excel::create('paytrip_example', function ($excel) use ($cus_list) {

            $excel->sheet('sheet name', function ($sheet) use ($cus_list) {

                $sheet->fromArray($cus_list, null, 'A1', false, false);

                $sheet->cells('A1:AN1', function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setFontWeight("bold");
                    $cells->setFontColor('#000000');
                    $cells->setFontSize('12');
                });

                $sheet->cells('A2:AN2', function ($cells) {
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
        $count_purpose = count($request->purpose_add);

        $m = 0;
        $n = 0;
        $l = 0;

        for ($i = 0; $i < $count_purpose; $i++) {

            if ($request->purpose_add[$i] != "") {

                $parmsadd = [
                    "PurposeID" => $request->purpose_add[$i],
                    "DestinationID" => $request->destination_add[$i],
                    "UserID" => $user_id,
                    "PayTrip" => $request->pay_amount_add[$i],
                ];

                $call_api = $this->call_api_by_parameter("webAddPayTrip", $parmsadd);
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

    public function export()
    {
        $user_id = Session::get("ID");
        $data_json = $this->call_api_by_parameter("webGetPayTripExport", ['UserID' => $user_id]);

        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        } else {
            $data_encode = json_decode($data_json);
            $data = [];

            if ($data_encode->id) {

                $data = json_encode($data_encode->data);
                $data = json_decode($data, true);
            }

            return \Excel::create('PayTrip Export', function ($excel) use ($data) {

                $excel->sheet('Ticket Sheet', function ($sheet) use ($data) {

                    $sheet->fromArray($data, null, 'A1', true);

                });
            })->download('xlsx');
        }

    }
}
