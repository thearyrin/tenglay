<?php

namespace App\Http\Controllers\Admin\Setting\Driver;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class DriverController extends Controller
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

        $data_check = $this->check_permission("SYS013", "view");

        $user_id = Session::get("ID");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $data_json = $this->call_api_by_parameter("webGetDriver", ["UserID" => $user_id]);
        $data_group = $this->call_api_by_parameter("webGetGroup", ["UserID" => $user_id]);

        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection.");
        }

        $data_encode = json_decode($data_json);
        $group_decode = json_decode($data_group);

        $data['id'] = $data_encode->id;
        $data['driver_list'] = $data_encode->data;
        $data['group_id'] = $group_decode->id;
        $data['group_list'] = $group_decode->data;
        $data['user_id'] = $user_id;

        $data['add'] = CheckPemission($this->check_permission("SYS013", "add"));
        $data['edit'] = CheckPemission($this->check_permission("SYS013", "edit"));
        $data['delete'] = CheckPemission($this->check_permission("SYS013", "delete"));
        $data['view_driver'] = CheckPemission($this->check_permission("SYS015", "view"));

        return view('pages.backend.setting.driver.show', compact("data"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $date_in = '';
        if (!empty($request->date_in)) {
            $date_in = Carbon::createFromFormat("d/m/Y", $request->date_in)->format("Y-m-d");
        }

        $parmsadd = [
            "UserID" => $request->user_id,
            "CodeID" => $request->driver_number,
            "NameKh" => $request->khmer_name,
            "NameLatin" => $request->latin_name,
            "Gender" => $request->gender,
            "DateIn" => $date_in,
            "Position" => $request->position,
            "Department" => $request->department,
            "Place" => $request->place,
            "GroupID" => $request->group,
        ];

        $name = "webAddDriver";

        if ($request->id) {

            $name = "webEditDriver";

            $parmsadd = [
                "ID" => $request->id,
                "UserID" => $request->user_id,
                "CodeID" => $request->driver_number,
                "NameKh" => $request->khmer_name,
                "NameLatin" => $request->latin_name,
                "Gender" => $request->gender,
                "DateIn" => $date_in,
                "Position" => $request->position,
                "Department" => $request->department,
                "Place" => $request->place,
                "Status" => $request->status,
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
    public function destroy(Request $request)
    {
        $data_api = $this->call_api_by_parameter("webDeleteDriver", array("UserID" => $request->user_id, "ID" => $request->id));

        if ($data_api) {
            $decode = json_decode($data_api);
            if ($decode->id) {
                return response()->json([
                    "message" => "Data Driver Deleted"
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

        $data_api = $this->call_api_by_parameter("webGetDriver", ["UserID" => $user_id]);

        $driver_list = array();
        $driver_list[] = ['ID', 'Khmer Name', 'Latin Name', 'Gender', 'Date In', 'Position', 'Department', 'Place', 'Group', 'Status'];
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

                    $driver_list[] = array($row->CodeID, $row->NameKh, $row->NameLatin, $row->Gender, $row->DateIN, $row->Position, $row->Department, $row->Place, $row->Name, $status);
                    $i++;
                }
            }
            // Convert each member of the returned collection into an array,
            // and append it to the payments array.
            return \Excel::create('Driver List', function ($excel) use ($driver_list, $i) {

                $excel->sheet('sheet name', function ($sheet) use ($driver_list, $i) {


                    $sheet->cells('A1:J1', function ($cells) {

                        $cells->setAlignment('center');
                        $cells->setFontWeight("bold");
                        $cells->setFontColor('#000000');
                        $cells->setFontSize('12');
                    });

                    $sheet->setColumnFormat(array(
                        'E' => 'mm/dd/yyyy'
                    ));

                    $sheet->fromArray($driver_list, null, 'A1', false, false);
                });
            })->download('xlsx');
        }
    }


    //this function for import data fleet
    public function import(Request $request)
    {

        set_time_limit(1000);
        $group_id = Session::get("group_id");
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

//            \Excel::load($request->file_excel, function ($reader) {

            $user_id = Session::get("ID");

            foreach ($reader as $row) {

                $id = $row["id"];
                $khmer_name = $row['khmer_name'];
                $latin_name = $row['latin_name'];
                $gender = $row['gender'];
                $date_in = $row['date_in'];
                $position = $row['position'];
                $department = $row['department'];
                $place = $row['place'];

                if (!empty($date_in)) {
                    $date_in = Carbon::createFromFormat("d/m/Y", $date_in)->format("Y-m-d");
                }

                $parmsadd = [
                    "UserID" => $user_id,
                    "CodeID" => $id,
                    "NameKh" => $khmer_name,
                    "NameLatin" => $latin_name,
                    "Gender" => $gender,
                    "DateIn" => $date_in,
                    "Position" => $position,
                    "Department" => $department,
                    "Place" => $place,
                    "GroupID" => $group_id
                ];

                $call_api = $this->call_api_by_parameter("webAddDriver", $parmsadd);

                $decode = json_decode($call_api);

                if ($decode->id) {
                    $j++;
                } else {
                    $k++;
                }

                $i++;
            }
//            });

            return response()->json([
                'message' => "Your file upload completed. Total data is " . $i . " rows. Inserted: " . $j . " rows, Not insert: " . $k . " rows"
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    //this function to download example of driver
    public function download()
    {
        $driver_list = array();
        $driver_list[] = ['ID', 'Khmer Name', 'Latin Name', 'Gender', 'Date In', 'Position', 'Department', 'Place'];
        $driver_list[] = ["8000167", "តឹក វ៉ាន់ណា", "TOEK Vanna", "ប្រុស", "07/11/2018", "Small Truck Driver", "Trucking in TLD", "Dry Port"];
        $driver_list[] = ["8000165", "ឯម ឌីន", "EM Din", "ប្រុស", "07/09/2018", "Small Truck Driver", "Trucking in TLD", "Dry Port"];
        return \Excel::create('driver_example', function ($excel) use ($driver_list) {

            $excel->sheet('sheet name', function ($sheet) use ($driver_list) {

                $sheet->fromArray($driver_list, null, 'A1', false, false);
                $sheet->cells('A1:H1', function ($cells) {

                    $cells->setAlignment('center');
                    $cells->setFontWeight("bold");
                    $cells->setFontColor('#000000');
                    $cells->setFontSize('12');
                });

                $sheet->setColumnFormat(array(
                    'E' => 'mm/dd/yyyy'
                ));
            });
        })->download('xlsx');
    }
}
