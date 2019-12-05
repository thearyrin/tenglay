<?php

namespace App\Http\Controllers\Admin\Setting\Destination;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DestinationController extends Controller
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

        $data_check = $this->check_permission("SYS018", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $data_json = $this->call_api_by_parameter("webGetDestination", ['UserID' => $user_id]);
        $data_group = $this->call_api_by_parameter("webGetGroup", ["UserID" => $user_id]);

        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        }

        $data_encode = json_decode($data_json);
        $group_decode = json_decode($data_group);

        $data['id'] = $data_encode->id;
        $data['list'] = $data_encode->data;
        $data['user_id'] = $user_id;
        $data['group_id'] = $group_decode->id;
        $data['group_list'] = $group_decode->data;

        $data['add'] = CheckPemission($this->check_permission("SYS018", 'add'));
        $data['edit'] = CheckPemission($this->check_permission("SYS018", 'edit'));
        $data['delete'] = CheckPemission($this->check_permission("SYS018", 'delete'));

        return view('pages.backend.setting.destination.show', compact("data"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $total = 0;
        if (!empty($request->fuel)) {
            $total = $request->fuel;
        }
        if (!empty($request->add_fuel)) {
            $total = ($total + $request->add_fuel);
        }



        if ($request->id) {

            $name = "webEditDestination";

            $parmsadd = [
                "ID" => $request->id,
                "UserID" => $request->user_id,
                "Code" => $request->destination_code,
                "Name" => $request->destination_name,
                "Distance" => $request->distance,
                "Fuel" => $request->fuel,
                "AddFuel" => $request->add_fuel,
                "Total" => $total,
                "Note" => $request->note,
                "Status" => $request->status,
                "GroupID" => $request->group,
                "RoundTrip" => $request->round_trip,
            ];
        }else{
            $parmsadd = [
                "UserID" => $request->user_id,
                "Code" => $request->destination_code,
                "Name" => $request->destination_name,
                "Distance" => $request->distance,
                "Fuel" => $request->fuel,
                "AddFuel" => $request->add_fuel,
                "Total" => $total,
                "Note" => $request->note,
                "Status" => $request->status,
                "GroupID" => $request->group,
                "RoundTrip" => $request->round_trip,
            ];

            $name = "webAddDestination";
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
        $data_api = $this->call_api_by_parameter("webDeleteDestination", array("UserID" => $request->user_id, "ID" => $request->id));

        if ($data_api) {
            $decode = json_decode($data_api);
            if ($decode->id) {
                return response()->json([
                    "message" => "Data Destination Deleted"
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

        $data_api = $this->call_api_by_parameter("webGetDestination", ["UserID" => $user_id]);

        $destination_list = array();
        $destination_list[] = ['ID', 'Code', 'Address', 'Distance', 'Fuel', 'Add Fuel', 'Total Fuel', 'RoundTrip', 'Group', 'Note', 'Status'];
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

                    $distance = '';
                    if ($row->Distance != "") {
                        $distance = $row->Distance . " Km";
                    }

                    $fuel = '';
                    if ($row->Fuel != "") {
                        $fuel = $row->Fuel . " L";
                    }

                    $add_fuel = '';
                    if ($row->AddFuel != "") {
                        $add_fuel = $row->AddFuel . " L";
                    }

                    $total = '';
                    if ($row->Total != "") {
                        $total = $row->Total . " L";
                    }

                    $destination_list[] = array($row->ID, $row->Code, $row->Name, $distance, $fuel, $add_fuel, $total, $row->RoundTrip, $row->GroupName, $row->Note, $status);
                    $i++;
                }
            }
            // Convert each member of the returned collection into an array,
            // and append it to the payments array.
            return \Excel::create('Destination List', function ($excel) use ($destination_list, $i) {

                $excel->sheet('sheet name', function ($sheet) use ($destination_list, $i) {


                    $sheet->cells('A1:K1', function ($cells) {

                        $cells->setAlignment('center');
                        $cells->setFontWeight("bold");
                        $cells->setFontColor('#000000');
                        $cells->setFontSize('12');
                    });

                    $sheet->fromArray($destination_list, null, 'A1', false, false);
                });
            })->download('xlsx');
        }
    }

    //this function for import data fleet
    public function import(Request $request)
    {

        set_time_limit(1000);
        $group_id = Session::get("group_id");

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

                $distance = $row['distance'];
                if ((strpos($distance, 'km')) || strpos($distance, 'Km') || (strpos($distance, ' km')) || (strpos($distance, ' Km'))) {
                    $array = preg_split('/(Km|km| Km| Km)/', $distance);
                    $distance = $array[0];
                }

                $fuel = $row['fuel'];
                if ((strpos($fuel, 'l')) || strpos($fuel, 'L') || (strpos($fuel, ' l')) || (strpos($fuel, ' L'))) {
                    $array = preg_split('/(L|l| l| L)/', $fuel);
                    $fuel = $array[0];
                }

                $add_fuel = $row['add_fuel'];
                if ((strpos($add_fuel, 'l')) || strpos($add_fuel, 'L') || (strpos($add_fuel, ' l')) || (strpos($add_fuel, ' L'))) {
                    $array = preg_split('/(L|l| l| L)/', $add_fuel);
                    $add_fuel = $array[0];
                }

                $total = 0;
                if ($fuel != "") {
                    $total = $fuel;
                }

                if ($add_fuel != "") {
                    $total = ($total + $add_fuel);
                }

                $parmsadd = [
                    "UserID" => $user_id,
                    "Code" => $row['code'],
                    "Name" => $row['address'],
                    "Distance" => $distance,
                    "Fuel" => $fuel,
                    "AddFuel" => $add_fuel,
                    "Total" => $total,
                    "Note" => $row['note'],
                    "Status" => 1,
                    "GroupID" => $group_id,
                    "RoundTrip" => $row["round_trip"],
                ];

                $call_api = $this->call_api_by_parameter("webAddDestination", $parmsadd);
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
        $cus_list[] = ['Code', 'Address', 'Distance', 'Fuel', 'Add Fuel', 'Round Trip', 'Note'];
        $cus_list[] = ['PP-ទៅមកផែភ្នំពេញ (Exp, MT​ & ទៅទទេរ)', 'កំពង់ផែភ្នំពេញ', '60Km', '50 L', '5 L', '', ''];
        $cus_list[] = ['BV-​ Exp ទៅមកបាវិត(ទៅសាច់)', 'ក្រុងបាវិត', '180Km', '150 L', '10 L', '', ''];
        $cus_list[] = ['BV-ទៅមកបាវិត (MT​ & ទៅទទេរ)', 'ក្រុងបាវិត', '180Km', '140 L', '10 L', '', ''];

        return \Excel::create('destination_example', function ($excel) use ($cus_list) {

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
}
