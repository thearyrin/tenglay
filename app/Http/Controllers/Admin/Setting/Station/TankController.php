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

class TankController extends Controller
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
    public function get_tank(Request $request)
    {

        $parm_noz = [
            "UserID" => $request->user_id,
            "StationNumber" => $request->id,
        ];

        $call_api = $this->call_api_by_parameter("webGetTank", $parm_noz);

        if ($call_api) {

            $decode = json_decode($call_api);
            $api_grade = $this->call_api_by_parameter("webGetGrade", ["UserID" => $request->user_id, "StationNumber" => $request->id]);
            $decode_grade = json_decode($api_grade);
            $delete = CheckPemission($this->check_permission("SYS011", 'delete'));

            return response()->json([
                'error' => false,
                'id' => $decode->id,
                'data' => $decode->data,
                'id_grade' => $decode_grade->id,
                'data_grade' => $decode_grade->data,
                'delete' => $delete
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to save data nozzle
    public function save_tank(Request $request)
    {
        $parm = [
            "UserID" => $request->user_id,
            "StationNumber" => $request->station_id,
            "TankNumber" => $request->tank_number,
            "GradeNumber" => $request->grade_name,
            "Capacity" => $request->capacity,
            "ProductVolume" => $request->product_volume,
            "WaterVolume" => $request->water_volume,
            "ProductHeight" => $request->product_height,
            "WaterHeight" => $request->water_height,
            "Ullage" => $request->ullage,
            "TankName" => $request->tank_name,
        ];

//        dd($parm);
        $call_api = $this->call_api_by_parameter("webAddTank", $parm);
        $delete = CheckPemission($this->check_permission("SYS011", 'delete'));

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
    public function delete_tank(Request $request)
    {
        $call_api = $this->call_api_by_parameter("webDeleteTank", ["UserID" => $request->user_id, "TankID" => $request->id]);
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
    /**-----------------------End Block For Nozzle Management----------------------*/
}