<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 5/17/2018
 * Time: 5:10 PM
 */

namespace App\Http\Controllers\Admin\Setting\Station;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NozzleController extends Controller
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

    /**-----------------------Start Block For Nozzle Management----------------------*/
    //this function is getting data nozzle and grade to show of each station
    public function get_nozzle(Request $request)
    {

        $user_id = Session::get("ID");

        $parm_noz = [
            "UserID" => $request->user_id,
            "StationNumber" => $request->station,
            "PumpNumber" => $request->pump
        ];

        $call_api = $this->call_api_by_parameter("webGetNozzle", $parm_noz);

        if ($call_api) {

            $decode_noz = json_decode($call_api);
            $api_grade = $this->call_api_by_parameter("webGetGrade", ["UserID" => $request->user_id, "StationNumber" => $request->station]);

            $decode_grade = json_decode($api_grade);

            $call_tank = $this->call_api_by_parameter("webGetTank", ["UserID" => $user_id, "StationNumber" => $request->station]);
            $decode_tank = json_decode($call_tank);
            $delete = CheckPemission($this->check_permission("SYS012", 'delete'));

            return response()->json([
                'id_noz' => $decode_noz->id,
                'id_grade' => $decode_grade->id,
                'id_tank' => $decode_tank->id,
                'data_noz' => $decode_noz->data,
                'data_grade' => $decode_grade->data,
                'data_tank' => $decode_tank->data,
                'delete' => $delete
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to save data nozzle
    public function save_nozzle(Request $request)
    {
        $parm = [
            "UserID" => $request->user_id,
            "StationNumber" => $request->station_id,
            "PumpNumber" => $request->pump_id,
            "NozzleNumber" => $request->nozzle_id,
            "GradeNumber" => $request->grade_id,
            "TankNumber" => $request->tank_id,
            "Sort" => $request->sort_id,
        ];
//        dd($parm);
        $call_api = $this->call_api_by_parameter("webAddNozzle", $parm);
        $delete = CheckPemission($this->check_permission("SYS012", 'delete'));
        if ($call_api) {
            $decode = json_decode($call_api);
            return response()->json([
                'id' => $decode->id,
                'msg' => $decode->message,
                'data' => $decode->data,
                'delete' => $delete
            ]);
        }
        return response()->json([
            'error' => "You are losing your connection."
        ]);
    }

    //this function to delete data nozzle
    public function delete_nozzle(Request $request)
    {
        $call_api = $this->call_api_by_parameter("webDeleteNozzle", ["UserID" => $request->user_id, "NozzleID" => $request->nozzle_id]);
        if ($call_api) {
            $decode = json_decode($call_api);

            return response()->json([
                'id' => $decode->id,
                'msg' => $decode->message
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection."
        ]);
    }

    //print nozzle barcode
    public function print_nozzle(Request $request)
    {

        $station_id = 0;
        $data_station = '';
        $pump_id = 0;
        $pump_data = '';
        $nozzle_data = '';
        $pump_status = 0;
        $nozzle_status = 0;

        $user_id = Session::get("ID");

        $data_check = $this->check_permission("SYS010");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $call_api = $this->call_api_by_parameter("webGetStation", ["UserID" => $user_id]);

        if ($call_api) {

            $decode_station = json_decode($call_api);
            $station_status = $decode_station->id;
            $filter = 0;

            if ($request->isMethod('post')) {

                $station_id = $request->station;
                $filter = 1;

            } else {

                if (count($decode_station->data) > 1) {

                    if ($decode_station->data[0]->StationName != "All") {

                        $station_id = $decode_station->data[0]->StationNumber;
                    } else {
                        $station_id = $decode_station->data[1]->StationNumber;
                    }

                } else {
                    $station_id = $decode_station->data[0]->StationNumber;
                }

            }
            $data_station = $decode_station->data;

            if ($station_id) {

                $call_api_pump = $this->call_api_by_parameter("webGetPumpData", ["UserID" => $user_id, "StationNumber" => $station_id]);

                $decode_pump = json_decode($call_api_pump);

                if ($decode_pump->id) {

                    $pump_id = $decode_pump->data[0]->PumpNumber;
                    $pump_data = $decode_pump->data;
                }

                $pump_status = $decode_pump->id;
            }


            if ($request->isMethod('post')) {
                $pump_id = $request->pump;
            }


            if ($pump_id != '') {

                if ($pump_id == 0) {
                    $nozzle_data = [];
                    $nozzle_status = [];
                    foreach ($pump_data as $item) {

                        $call_api_nozzle[$item->PumpNumber] = $this->call_api_by_parameter("webGetNozzle", ["UserID" => $user_id, "StationNumber" => $station_id, "PumpNumber" => $item->PumpNumber]);

                        $decode_nozzle[$item->PumpNumber] = json_decode($call_api_nozzle[$item->PumpNumber]);

                        $nozzle_data[$item->PumpNumber] = $decode_nozzle[$item->PumpNumber]->data;
                        $nozzle_status[$item->PumpNumber] = $decode_nozzle[$item->PumpNumber]->id;
                    }
                } else {
                    $nozzle_data = '';
                    $nozzle_status = '';
                    $call_api_nozzle = $this->call_api_by_parameter("webGetNozzle", ["UserID" => $user_id, "StationNumber" => $station_id, "PumpNumber" => $pump_id]);

                    $decode_nozzle = json_decode($call_api_nozzle);

                    $nozzle_data = $decode_nozzle->data;
                    $nozzle_status = $decode_nozzle->id;
                }

            }

            $data['status_station'] = $station_status;
            $data['station_id'] = $station_id;
            $data['station_data'] = $data_station;
            $data['status_pump'] = $pump_status;
            $data['pump_id'] = $pump_id;
            $data['pump_data'] = $pump_data;
            $data['status_nozzle'] = $nozzle_status;
            $data['nozzle_data'] = $nozzle_data;
            $data['filter'] = $filter;

            return view('pages.backend.setting.station.barcode', compact('data'));
        }

        return redirect('admin')->withErrors("You are losing your connection");

    }
    /**-----------------------End Block For Nozzle Management----------------------*/
}