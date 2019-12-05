<?php

namespace App\Http\Controllers\Admin\Setting\Fleet;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FleetController extends Controller
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

        $data_check = $this->check_permission("SYS014", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $data_json = $this->call_api_by_parameter("webGetFleetDriverList", ['UserID' => $user_id]);
        $data_group = $this->call_api_by_parameter("webGetGroup", ["UserID" => $user_id]);

        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        }

        $data_encode = json_decode($data_json);
        $group_decode = json_decode($data_group);

        $call_grade = $this->call_api_by_parameter("webGetAllGrade", ['UserGet' => $user_id, 'StationNumber' => 0]);
//        $call_driver = $this->call_api_by_parameter("webGetDriverActive", ['UserID' => $user_id]);
        $data_grade = json_decode($call_grade);
//        $data_driver = json_decode($call_driver);

        $data['id'] = $data_encode->id;
        $data['list'] = $data_encode->data;
        $data['id_grade'] = $data_grade->id;
        $data['data_grade'] = $data_grade->data;
        $data['group_id'] = $group_decode->id;
        $data['group_list'] = $group_decode->data;
        $data['user_id'] = $user_id;
//        $data['id_driver'] = $data_driver->id;
//        $data['list_driver'] = $data_driver->data;
        $data['add'] = CheckPemission($this->check_permission("SYS014", "add"));
        $data['edit'] = CheckPemission($this->check_permission("SYS014", "edit"));
        $data['delete'] = CheckPemission($this->check_permission("SYS014", "delete"));
        $data['fleet_driver_check'] = CheckPemission($this->check_permission("SYS015", "view"));
        $data['fleet_driver_add'] = CheckPemission($this->check_permission("SYS015", "add"));

        return view('pages.backend.setting.fleet.show', compact("data"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $parmsadd = [
            "UserID" => $request->user_id,
            "PlateNumber" => $request->plate_number,
            "GradeID" => $request->product_grade,
            "Team" => $request->team,
            "Status" => $request->status,
            "FuelAdd" => $request->fuel_add,
            "GroupID" => $request->group,
        ];

        $name = "webAddFleet";

        if ($request->id) {

            $name = "webEditFleet";

            $parmsadd = [
                "FleetID" => $request->id,
                "UserID" => $request->user_id,
                "PlateNumber" => $request->plate_number,
                "GradeID" => $request->product_grade,
                "Team" => $request->team,
                "Status" => $request->status,
                "FuelAdd" => $request->fuel_add,
                "Enable" => $request->enable,
                "GroupID" => $request->group,
            ];
        }
//        dd($parmsadd);

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
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data_api = $this->call_api_by_parameter("webDeleteFleet", array("FleetID" => $request->id));

        if ($data_api) {
            $decode = json_decode($data_api);
            if ($decode->id) {
                return response()->json([
                    "message" => "Data Fleet Deleted"
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

        $data_api = $this->call_api_by_parameter("webGetFleetDriverList", ["UserID" => $user_id]);

        $fleet_list = array();
        $fleet_list[] = ['ID', 'Plate Number', 'Team', 'Fuel Add', 'Driver1', "Driver1 ID", 'Driver2', 'Driver2 ID', 'Grade', 'Group', 'Status', 'Active'];
        if ($data_api == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        } else {
            $data_json = json_decode($data_api);
            $i = 1;
            if ($data_json->id) {
                foreach ($data_json->data as $row) {
                    $status = "Available";
                    if ($row->Status == 1) {
                        $status = "Using";
                    } elseif ($row->Status == 2) {
                        $status = "Maintenance";
                    } elseif ($row->Status == 3) {
                        $status = "Dangerousness";
                    } elseif ($row->Status == 4) {
                        $status = "Broken";
                    }

                    $enable = '';
                    if ($row->Enable == 1) {
                        $enable = "Active";
                    } else if ($row->Enable == 0) {
                        $enable = "Inactive";
                    }

                    $fuel_add = '';
                    if ($row->FuelAdd == 1) {
                        $fuel_add = "2";
                    }

                    $fleet_list[] = array($row->ID, $row->PlateNumber, $row->Team, $fuel_add, $row->Driver1, $row->Code1,
                        ($row->CountNum > 1 ? $row->Driver2 : ""), ($row->CountNum > 1 ? $row->Code2 : ""), $row->GradeDescription,
                        $row->Name, $status, $enable);
                    $i++;
                }
            }
            // Convert each member of the returned collection into an array,
            // and append it to the payments array.
            return \Excel::create('Truck List', function ($excel) use ($fleet_list, $i) {

                $excel->sheet('sheet name', function ($sheet) use ($fleet_list, $i) {


                    $sheet->cells('A1:L1', function ($cells) {

                        $cells->setAlignment('center');
                        $cells->setFontWeight("bold");
                        $cells->setFontColor('#000000');
                        $cells->setFontSize('12');
                    });
                    $sheet->fromArray($fleet_list, null, 'A1', false, false);

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
            $user_id = Session::get("ID");

            foreach ($reader as $row) {

                $plate_number = $row["plate_number"];
                $team = $row['team'];
                $product_grade = $row['product_grade'];
                $status = $row['status'];
                $fuel_add = $row['fuel_add'];
                $status_num = 0;

                if ($status == "Available") {
                    $status_num = 0;
                } else if ($status == "Using") {
                    $status_num = 1;
                } else if ($status == "Maintenance") {
                    $status_num = 2;
                } else if ($status == "Dangerousness") {
                    $status_num = 3;
                } else if ($status == "Broken") {
                    $status_num = 4;
                }

                $grade = 1;
                if ($product_grade == "Dissel") {
                    $grade = 1;
                } else if ($product_grade == "Regular") {
                    $grade = 2;
                } else if ($product_grade == "Super") {
                    $grade = 3;
                }

                if ($fuel_add != "") {
                    $fuel_add = 1;
                }

                $parmsadd = [
                    "UserID" => $user_id,
                    "PlateNumber" => $plate_number,
                    "GradeID" => $grade,
                    "Team" => $team,
                    "Status" => $status_num,
                    "FuelAdd" => $fuel_add,
                    "GroupID" => $group_id,
                ];

                $call_api = $this->call_api_by_parameter("webAddFleet", $parmsadd);
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

    /**Start Fleet Mapping Driver here**/
    //this function for getting data fleet mapping with driver
    public function fleet_driver(Request $request)
    {

        $call_api = $this->call_api_by_parameter("webGetFleetDriver", ["FleetID" => $request->fleet_id]);

        if ($call_api == false) {
            return response()->json([
                "error" => "You are losing your connection"
            ]);
        }

        $decode = json_decode($call_api);
        $delete = CheckPemission($this->check_permission("SYS015", "delete"));

        return response()->json([
            'error' => false,
            'id' => $decode->id,
            'data' => $decode->data,
            'delete' => $delete
        ]);
    }

    //this function for adding data fleet mapping with driver
    public function save_fleet_driver(Request $request)
    {
        $call_api = $this->call_api_by_parameter("webAddFleetDriver", ["FleetID" => $request->fleet_id, "DriverID" => $request->driver_id]);

        if ($call_api == false) {
            return response()->json([
                'error' => "You are losing your connection"
            ]);
        }

        $decode = json_decode($call_api);
        $delete = CheckPemission($this->check_permission("SYS015", "delete"));

        if ($decode->id) {
            return response()->json([
                'error' => false,
                'message' => "Data fleet mapping with driver saved",
                'data' => $decode->data[0],
                'id' => $decode->id,
                'delete' => $delete
            ]);
        }

        return response()->json([
            'error' => false,
            'message' => $decode->message,
            'id' => $decode->id
        ]);
    }

    //this function for deleting data fleet mapping with driver
    public function delete_fleet_driver(Request $request)
    {

        $call_api = $this->call_api_by_parameter("webDeleteFleetDriver", ["ID" => $request->fleet_driver_id]);

        if ($call_api) {
            $decode = json_decode($call_api);
            if ($decode->id) {
                return response()->json([
                    'id' => $decode->id,
                    'msg' => "Fleet mapping Driver data deleted"
                ]);
            }
            return response()->json([
                'id' => $decode->id,
                'msg' => $decode->message,
            ]);
        }
        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function for example of downloading file excel
    public function download()
    {
        $fleet_list = array();

        $fleet_list[] = ['Plate Number', 'Team', 'Fuel Add', 'Product Grade', 'Status'];
        $fleet_list[] = ["3B-9727", "Bavet", "", "Dissel", "Available"];
        $fleet_list[] = ["3C-5551", "Bavet", "2", "Dissel", "Available"];

        // Convert each member of the returned collection into an array,
        // and append it to the payments array.
        return \Excel::create('truck_example', function ($excel) use ($fleet_list) {

            $excel->sheet('sheet name', function ($sheet) use ($fleet_list) {

                $sheet->fromArray($fleet_list, null, 'A1', false, false);
                $sheet->cells('A1:E1', function ($cells) {

                    $cells->setAlignment('center');
                    $cells->setFontWeight("bold");
                    $cells->setFontColor('#000000');
                    $cells->setFontSize('12');
                });
            });
        })->download('xlsx');
    }

    //this function for export data driver fleet
    public function export_fleet_driver()
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $user_id = Session::get("ID");

        $data_api = $this->call_api_by_parameter("webGetFleetDriverExport", ["UserID" => $user_id]);

        $fleet_list = array();
        $fleet_list[] = ['No', 'Plate Number', 'Team', 'Driver ID', 'Driver Name'];

        if ($data_api == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        } else {

            $data_json = json_decode($data_api);
            $i = 1;
            if ($data_json->id) {
                foreach ($data_json->data as $row) {

                    $fleet_list[] = array($i, $row->PlateNumber, $row->Team, $row->CodeID, $row->NameKh);
                    $i++;
                }
            }
            // Convert each member of the returned collection into an array,
            // and append it to the payments array.
            return \Excel::create('Truck Driver List', function ($excel) use ($fleet_list, $i) {

                $excel->sheet('sheet name', function ($sheet) use ($fleet_list, $i) {


                    $sheet->cells('A1:E1', function ($cells) {

                        $cells->setAlignment('center');
                        $cells->setFontWeight("bold");
                        $cells->setFontColor('#000000');
                        $cells->setFontSize('12');
                    });

                    $sheet->fromArray($fleet_list, null, 'A1', false, false);
                });
            })->download('xlsx');
        }
    }

    //this function for importing data driver fleet
    public function import_fleet_driver(Request $request)
    {

        set_time_limit(1000);
        $validator = Validator::make(
            [
                'file' => $request->file_excel_fleet_driver,
                'extension' => strtolower($request->file_excel_fleet_driver->getClientOriginalExtension()),
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

            $reader = \Excel::load($request->file_excel_fleet_driver)->toArray();

            $user_id = Session::get("ID");

            foreach ($reader as $row) {

//                echo $row['plate_number']." ".$row['driver_id']."<br/>";

                $call_fleet = $this->call_api_by_parameter("webGetFleetByID", ["PlateNumber" => $row['plate_number']]);
                $call_driver = $this->call_api_by_parameter("webGetDriverByID", ["CodeID" => $row['driver_id']]);

                if ($call_fleet && $call_driver) {

                    $decode_fleet = json_decode($call_fleet);
                    $decode_driver = json_decode($call_driver);

                    if ($decode_fleet->id && $decode_driver->id) {

                        $parmsadd = [
                            "FleetID" => $decode_fleet->data[0]->ID,
                            "DriverID" => $decode_driver->data[0]->ID
                        ];
//                        echo $decode_fleet->data[0]->ID . " " . $decode_driver->data[0]->ID."<br/>";
                        $delete_map = $this->call_api_by_parameter("webDeleteDriverMappingFleet", ["FleetID" => $decode_fleet->data[0]->ID]);
//                        dd($delete_map);

                        $call_api = $this->call_api_by_parameter("webAddFleetDriver", $parmsadd);
                        $decode = json_decode($call_api);

                        if ($decode->id) {
                            $j++;
                        } else {
                            $k++;
                        }
                    } else {
                        $k++;
                    }
                } else {
                    $k++;
                }

                $i++;
            }

//            dd();

            return response()->json([
                'message' => "Your file upload completed. Total data is " . $i . " rows. Inserted: " . $j . " rows, Not insert: " . $k . " rows"
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    //this function for export example of driver fleet
    //this function for example of downloading file excel
    public function download_fleet_driver()
    {
        $fleet_list = array();

        $fleet_list[] = ['Plate Number', 'Driver ID'];
        $fleet_list[] = ["3B-9727", "7000131"];
        $fleet_list[] = ["3B-9727", "7000190"];
        $fleet_list[] = ["3D-0783", "7000190"];
        $fleet_list[] = ["3D-0783", "7000225"];

        // and append it to the payments array.
        return \Excel::create('truck_driver_example', function ($excel) use ($fleet_list) {

            $excel->sheet('sheet name', function ($sheet) use ($fleet_list) {

                $sheet->fromArray($fleet_list, null, 'A1', false, false);
                $sheet->cells('A1:B1', function ($cells) {

                    $cells->setAlignment('center');
                    $cells->setFontWeight("bold");
                    $cells->setFontColor('#000000');
                    $cells->setFontSize('12');
                });
            });
        })->download('xlsx');
    }
}
