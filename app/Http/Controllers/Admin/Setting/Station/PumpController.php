<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 5/17/2018
 * Time: 5:08 PM
 */

namespace App\Http\Controllers\Admin\Setting\Station;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PumpController extends Controller
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

    /**-----------------------Start Block For Pump Management----------------------*/
    //this function is for getting data pump
    public function get_pump(Request $request)
    {

        $call_api = $this->call_api_by_parameter("webGetPump", ["UserID" => $request->user_id, "StationNumber" => $request->station_number]);
        $delete = CheckPemission($this->check_permission("SYS010", 'delete'));

        if ($call_api == false) {
            return response()->json([
                "error" => "You are losing your connection"
            ]);
        }

        $decode = json_decode($call_api);
        return response()->json([
            'error' => false,
            'id' => $decode->id,
            'data' => $decode->data,
            'delete' => $delete
        ]);
    }

    //this function is for saving data pump
    public function save_pump(Request $request)
    {

        $call_api = $this->call_api_by_parameter("webAddPump", ["UserID" => $request->user_id, "StationNumber" => $request->station_number, "PumpNumber" => $request->pump_number]);
        if ($call_api == false) {
            return response()->json([
                'error' => "You are losing your connection"
            ]);
        }

        $decode = json_decode($call_api);
        $delete = CheckPemission($this->check_permission("SYS010", 'delete'));
        if ($decode->id) {
            return response()->json([
                'error' => false,
                'message' => "Data Pump Saved",
                'data' => $decode->data[0],
                'id' => $decode->id,
                'nozzle_per' => Session::get("check_nozzle"),
                'delete' => $delete
            ]);
        }

        return response()->json([
            'error' => false,
            'message' => $decode->message,
            'id' => $decode->id
        ]);
    }

    //this function is for deleting data pump
    public function delete_pump(Request $request)
    {
//        dd(["UserID"=>$request->user_id,"PumpID"=> $request->pump_id]);
        $call_api = $this->call_api_by_parameter("webDeletePump", ["UserID" => $request->user_id, "PumpID" => $request->pump_id]);
//        dd($call_api);
        if ($call_api) {
            $decode = json_decode($call_api);
            if ($decode->id) {
                return response()->json([
                    'id' => $decode->id,
                    'msg' => "Pump Data Deleted"
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

    //this function to get image for downloading
    public function download(Request $request)
    {

        $call_api = $this->call_api_by_parameter("webGetPumpQRCodeBase64", ["strCode" => $request->qr_barcode]);

        if ($call_api) {

            $decode = json_decode($call_api);
            if ($decode->id) {
                return response()->json([
                    "image" => $decode->data
                ]);
            }

            return response()->json([
                "image" => $decode->message
            ]);
        }

        return response()->json([
            "error" => "You are losing your connection."
        ]);
    }

    //this function to get pump by station id
    public function list_pump(Request $request)
    {

        $user_id = Session::get("ID");
        $call_api_pump = $this->call_api_by_parameter("webGetPump", ["UserID" => $user_id, "StationNumber" => $request->station_id]);
        if ($call_api_pump) {

            $decode_pump = json_decode($call_api_pump);

            return response()->json([
                'id' => $decode_pump->id,
                'data' => $decode_pump->data,
                'msg' => $decode_pump->message
            ]);
        }
        return response()->json([
            'error' => "You are losing your connection."
        ]);
    }
    /**-----------------------End Block For Pump Management----------------------*/
}