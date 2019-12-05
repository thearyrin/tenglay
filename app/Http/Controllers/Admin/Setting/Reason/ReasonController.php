<?php

namespace App\Http\Controllers\Admin\Setting\Reason;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ReasonController extends Controller
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

        $data_check = $this->check_permission("SYS016", 'view');

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $data_json = $this->call_api_by_parameter("webGetReason", ['UserID' => $user_id]);

        $data_group = $this->call_api_by_parameter("webGetGroup", ["UserID" => $user_id]);

        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        }

        $data_encode = json_decode($data_json);
        $group_decode = json_decode($data_group);

        $data['id'] = $data_encode->id;
        $data['reason_list'] = $data_encode->data;
        $data['user_id'] = $user_id;
        $data['group_id'] = $group_decode->id;
        $data['group_list'] = $group_decode->data;

        $data['add'] = CheckPemission($this->check_permission("SYS016", 'add'));
        $data['edit'] = CheckPemission($this->check_permission("SYS016", 'edit'));
        $data['delete'] = CheckPemission($this->check_permission("SYS016", 'delete'));

        return view('pages.backend.setting.reason.show', compact("data"));
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
            "ReasonCode" => $request->reason_code,
            "Description" => $request->description,
            "Other" => $request->other,
            "Status" => $request->status,
            "Purpose" => $request->destination,
            "RoundTrip" => '',
            "Score" => $request->score,
            "GroupID" => $request->group,
        ];


        $name = "webAddReason";

        if ($request->id) {

            $name = "webEditReason";

            $parmsadd = [
                "ID" => $request->id,
                "UserID" => $request->user_id,
                "ReasonCode" => $request->reason_code,
                "Description" => $request->description,
                "Other" => $request->other,
                "Status" => $request->status,
                "Purpose" => $request->destination,
                "RoundTrip" => '',
                "Score" => $request->score,
                "GroupID" => $request->group,
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
        $data_api = $this->call_api_by_parameter("webDeleteReason", array("UserID" => $request->user_id, "ID" => $request->id));

        if ($data_api) {
            $decode = json_decode($data_api);
            if ($decode->id) {
                return response()->json([
                    "message" => "Data Reason Deleted"
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function export()
    {


        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $user_id = Session::get("ID");

        $data_api = $this->call_api_by_parameter("webGetReason", ["UserID" => $user_id]);

        $cus_list = array();
        $cus_list[] = ['ID', 'Code', 'Purpose', 'Destination', 'Description', 'Score', 'Group', 'Status'];
        if ($data_api == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        } else {
            $data_json = json_decode($data_api);
            $i = 1;
            if ($data_json->id) {
                foreach ($data_json->data as $row) {

                    $status = "Active";
                    if ($row->Status == 0) {
                        $status = "Inactive";
                    }

                    $cus_list[] = array($row->ID, $row->ReasonCode, $row->Reason, $row->Purpose, $row->Description, $row->Score, $row->Name, $status);
                    $i++;
                }
            }
            // Convert each member of the returned collection into an array,
            // and append it to the payments array.
            return \Excel::create('Purpose List', function ($excel) use ($cus_list, $i) {

                $excel->sheet('sheet name', function ($sheet) use ($cus_list, $i) {


                    $sheet->cells('A1:H1', function ($cells) {

                        $cells->setAlignment('center');
                        $cells->setFontWeight("bold");
                        $cells->setFontColor('#000000');
                        $cells->setFontSize('12');
                    });

                    $sheet->fromArray($cus_list, null, 'A1', false, false);
                });
            })->download('xlsx');
        }
    }

    //this function for import data fleet
    public function import(Request $request)
    {

        set_time_limit(1000);
        $validator = Validator::make(
            [
                'file' => $request->file_excel,
                'extension' => strtolower($request->file_excel->getClientOriginalExtension()),
            ],
            [
                'file' => 'required',
                'extension' => 'required|in:xlsx,xls',
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

            $reader = \Excel::load($request->file_excel)->toArray();

            $user_id = Session::get("ID");
            $group_id = Session::get("group_id");

            foreach ($reader as $row) {

                $parmsadd = [
                    "UserID" => $user_id,
                    "ReasonCode" => $row["code"],
                    "Description" => $row['purpose'],
                    "Other" => $row['description'],
                    "Status" => 1,
                    "Purpose" => $row['destination'],
                    "RoundTrip" => '',
                    "Score" => $row['score'],
                    "GroupID" => $group_id,
                ];

                $call_api = $this->call_api_by_parameter("webAddReason", $parmsadd);
                $decode = json_decode($call_api);

                if ($decode->id) {
                    $j++;
                } else {
                    $k++;
                }

                $i++;
            }

            return response()->json([
                'message' => "Your file upload completed. Total data is " . $i . " rows. Inserted: " . $j . " rows, Not insert: " . $k . " rows"
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
        $cus_list[] = ['Code', 'Purpose', 'Destination', 'Description', 'Score'];
        $cus_list[] = ['1', 'EXP,MT To FTY', 'EXPORT', 'យកសំបកទូកុងទីន័រទៅច្រកទំនិញរោងចក្រ', number_format(2, 2)];
        $cus_list[] = ['3', 'Exp,Laden pickup to TL', 'PULL-Exp', 'ទៅអូសសាច់ដែរផ្តាច់ ពីរោងចក្រមកផែរវិញ', number_format(1, 2)];

        return \Excel::create('purpose_example', function ($excel) use ($cus_list) {

            $excel->sheet('sheet name', function ($sheet) use ($cus_list) {

                $sheet->fromArray($cus_list, null, 'A1', false, false);
                $sheet->cells('A1:E1', function ($cells) {

                    $cells->setAlignment('center');
                    $cells->setFontWeight("bold");
                    $cells->setFontColor('#000000');
                    $cells->setFontSize('12');
                });
            });
        })->download('xlsx');
    }
}
