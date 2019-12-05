<?php

namespace App\Http\Controllers\Admin\Setting\Container;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ContainerController extends Controller
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
        $data_check = $this->check_permission("SYS019", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");
        $data_group = $this->call_api_by_parameter("webGetGroup", ["UserID" => $user_id]);
        if ($data_group == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        }
        $group_decode = json_decode($data_group);
        $data['group_id'] = $group_decode->id;
        $data['group_list'] = $group_decode->data;
        $data['user_id'] = $user_id;
        $data['add'] = CheckPemission($this->check_permission("SYS019", 'add'));
        $data['edit'] = CheckPemission($this->check_permission("SYS019", 'edit'));
        $data['delete'] = CheckPemission($this->check_permission("SYS019", 'delete'));

        return view('pages.backend.setting.container.show', compact("data"));
    }

    public function get_container(Request $request)
    {


        $user_id = Session::get("ID");

        $call_api = $this->call_api_by_parameter("webGetAllContainer", ["UserID" => $user_id]);
        if ($call_api == false) {
            return response()->json([]);
        }
        $data_encode = json_decode($call_api);
        if ($data_encode->id) {
            return response()->json($data_encode->data);
        }
        return response()->json([]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        if ($request->id) {

            $name = "webEditContainer";

            $parmsadd = [
                "ID" => $request->id,
                "UserID" => $request->user_id,
                "ContainerNumber" => $request->container_number,
                "Feet" => $request->feet,
                "Status" => $request->status,
                "GroupID" => $request->group,
            ];
        }else{
            
            $parmsadd = [
                "UserID" => $request->user_id,
                "ContainerNumber" => $request->container_number,
                "Feet" => $request->feet,
                "Status" => $request->status,
                "GroupID" => $request->group,
            ];

            $name = "webAddContainer";
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
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data_api = $this->call_api_by_parameter("webDeleteContainer", array("UserID" => $request->user_id, "ID" => $request->id));

        if ($data_api) {
            $decode = json_decode($data_api);
            if ($decode->id) {
                return response()->json([
                    "message" => "Data Container Deleted"
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

        $data_api = $this->call_api_by_parameter("webGetAllContainer", ["UserID" => $user_id]);

        $con_list = array();
        $con_list[] = ['ID', 'Container Number', 'Feet', 'Group', 'Status'];
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

                    $con_list[] = array($row->ID, $row->ContainerNumber, $row->Feet, $row->Name, $status);
                    $i++;
                }
            }
            // Convert each member of the returned collection into an array,
            // and append it to the payments array.
            return \Excel::create('Container List', function ($excel) use ($con_list, $i) {

                $excel->sheet('sheet name', function ($sheet) use ($con_list, $i) {


                    $sheet->cells('A1:E1', function ($cells) {

                        $cells->setAlignment('center');
                        $cells->setFontWeight("bold");
                        $cells->setFontColor('#000000');
                        $cells->setFontSize('12');
                    });
                    $sheet->fromArray($con_list, null, 'A1', false, false);

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

            $reader = \Excel::load($request->file_excel)->toArray();

//            \Excel::load($request->file_excel, function ($reader) {
//
            $user_id = Session::get("ID");
            $i = 0;
            $j = 0;
            $k = 0;
            foreach ($reader as $row) {

                $container_number = $row["container_number"];
                $feet = $row['feet'];

                $parmsadd = [
                    "UserID" => $user_id,
                    "ContainerNumber" => $container_number,
                    "Feet" => $feet,
                    "Status" => 1,
                    "GroupID" => $group_id,
                ];

                $call_api = $this->call_api_by_parameter("webAddContainer", $parmsadd);
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

    //this function to download example of excel
    public function download()
    {
        $cus_list = array();
        $cus_list[] = ['Container Number', 'Feet'];
        $cus_list[] = ['TCKU9970531', '45'];
        $cus_list[] = ['OOLU9044455', '45'];
        $cus_list[] = ['TRHU3877461', '20'];

        return \Excel::create('container_example', function ($excel) use ($cus_list) {

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
}
