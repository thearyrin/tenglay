<?php

namespace App\Http\Controllers\Admin\Setting\MTPickup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MTPickUpController extends Controller
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

        $data_check = $this->check_permission("SYS040", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $data_json = $this->call_api_by_parameter("webGetMTPickup", ['UserID' => $user_id]);

        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        }

        $data_encode = json_decode($data_json);

        $data['id'] = $data_encode->id;
        $data['list'] = $data_encode->data;
        $data['user_id'] = $user_id;

        $data['add'] = CheckPemission($this->check_permission("SYS040", 'add'));
        $data['edit'] = CheckPemission($this->check_permission("SYS040", 'edit'));
        $data['delete'] = CheckPemission($this->check_permission("SYS040", 'delete'));

        return view('pages.backend.setting.mtpickup.show', compact("data"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {

        if ($request->id) {

            $name = "webEditMTPickup";

            $parmsadd = [
                "UserID" => $request->user_id,
                "Name" => $request->name,
                "Lolo" => $request->lolo,
                "Note" => $request->note,
                "Currency" => "",
                "Symbol" => "",
                "ID" => $request->id,
                "Status" => $request->status,
                "Feet" => $request->feet,
            ];
        } else {
            $parmsadd = [
                "UserID" => $request->user_id,
                "Name" => $request->name,
                "Lolo" => $request->lolo,
                "Note" => $request->note,
                "Feet" => $request->feet,
                "Currency" => "",
                "Symbol" => ""
            ];

            $name = "webAddMTPickup";
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
        $data_api = $this->call_api_by_parameter("webDeleteMTPickup", array("UserID" => $request->user_id, "ID" => $request->id));

        if ($data_api) {
            $decode = json_decode($data_api);
            if ($decode->id) {
                return response()->json([
                    "message" => "Data MTPickup Deport Deleted"
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

        set_time_limit(1000);

        $user_id = Session::get("ID");

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
            foreach ($reader as $row) {

                $lolo = $row['lolo'];

                if ((strpos($lolo, '$')) || (strpos($lolo, ' $')) || (strpos($lolo, '$ '))) {
                    $array = preg_split('/($| $|$ )/', $lolo);
                    $lolo = $array[0];
                }

                $parmsadd = [
                    "UserID" => $user_id,
                    "Name" => $row['code'],
                    "Lolo" => $lolo,
                    "Note" => $row['note'],
                    "Currency" => '',
                    "Symbol" => '',
                    "Feet" => $row['feet'],
                ];

                $call_api = $this->call_api_by_parameter("webAddMTPickup", $parmsadd);
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


        } catch
        (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    //this function for download example purpose excel
    public function download()
    {

        $cus_list = array();
        $cus_list[] = ['Code', 'Feet', 'Lolo($)', 'Note'];
        $cus_list[] = ['TL_20', '20', '0', 'TL_20'];
        $cus_list[] = ['UN_20', '20', '15', 'UN_20'];
        $cus_list[] = ['NTS_20', '20', '15', 'NTS_20'];

        return \Excel::create('mtpickup_example', function ($excel) use ($cus_list) {

            $excel->sheet('sheet name', function ($sheet) use ($cus_list) {

                $sheet->fromArray($cus_list, null, 'A1', false, false);
                $sheet->cells('A1:D1', function ($cells) {

                    $cells->setAlignment('center');
                    $cells->setFontWeight("bold");
                    $cells->setFontColor('#000000');
                    $cells->setFontSize('12');
                });
            });
        })->download('xlsx');
    }

}
