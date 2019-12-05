<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 5/17/2018
 * Time: 4:36 PM
 */

namespace App\Http\Controllers\Admin\Setting\Station;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class StationController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $station_role = [
        "station_number" => "required|numeric",
        "station_name" => "required",
        "ip_address" => "required",
        "user_id" => "required",
    ];


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except("logout");
    }

    /**-----------------------Start Block For station Barcode Management----------------------*/
    //this function is for station indext
    public function station()
    {

        $data_check = $this->check_permission("SYS008", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");
        $call_api = $this->call_api_by_parameter("webGetStation", ["UserID" => $user_id]);


        $decode = json_decode($call_api);
        $data['id'] = $decode->id;
        $data['data'] = $decode->data;
        $data['user_id'] = $user_id;

        $data['grade_check'] = CheckPemission($this->check_permission("SYS009", 'view'));
        $data['grade_add'] = CheckPemission($this->check_permission("SYS009", 'add'));

        $data['tank_check'] = CheckPemission($this->check_permission("SYS011", 'view'));
        $data['tank_add'] = CheckPemission($this->check_permission("SYS011", 'add'));

        $data['pump_check'] = CheckPemission($this->check_permission("SYS010", 'view'));
        $data['pump_add'] = CheckPemission($this->check_permission("SYS010", 'add'));

        $data['nozzle_check'] = CheckPemission($this->check_permission("SYS012", 'view'));
        $data['nozzle_add'] = CheckPemission($this->check_permission("SYS012", 'add'));

        $data['add_station'] = CheckPemission($this->check_permission("SYS008", 'add'));
        $data['edit_station'] = CheckPemission($this->check_permission("SYS008", 'edit'));
        $data['delete_station'] = CheckPemission($this->check_permission("SYS008", 'delete'));

        return view("pages.backend.setting.station.station", compact('data'));
    }

    //this function is for saving station
    public function save_station(Request $request)
    {

        $validation = Validator::make($request->all(), $this->station_role);

        if ($validation->fails()) {
            return response()->json([
                'error' => $validation->errors()->first()
            ]);
        }

        $lat = '';
        $lng = "";

        if ($request->map_location) {

            $exp = explode(",", $request->map_location);
            $lat = $exp[0];
            $lng = $exp[1];
        }

        $status = 0;
        if ($request->status == "on" || $request->status == 1) {

            $status = 1;
        }

        if ($request->id) {
            $pars = [
                "UserID" => $request->user_id,
                "StationID" => $request->id,
                "StationNumber" => $request->station_number,
                "StationName" => $request->station_name,
                "StationIP" => $request->ip_address,
                "Latitude" => $lat,
                "Longitude" => $lng,
                "Active" => $status,
            ];
            $name_api = "webEditStation";

        } else {
            $pars = [
                "UserID" => $request->user_id,
                "StationNumber" => $request->station_number,
                "StationName" => $request->station_name,
                "StationIP" => $request->ip_address,
                "Latitude" => $lat,
                "Longitude" => $lng,
                "Active" => $status,
            ];
            $name_api = "webAddStation";
        }

        $call_api = $this->call_api_by_parameter($name_api, $pars);

        if ($call_api == false) {
            return response()->json([
                'error' => "You are losing your connection."
            ]);
        }

        $decode = json_decode($call_api);

        if ($decode->id) {

            return response()->json([
                'error' => false,
                "data" => $decode->data[0]
            ]);
        }

        return response()->json([
            'error' => $decode->message
        ]);

    }

    //this function is for deleting station
    public function delete_station(Request $request)
    {
        $call_api = $this->call_api_by_parameter("webDeleteStation", ['UserID' => $request->user_id, "StationID" => $request->id]);

        if ($call_api) {
            return response()->json([
                'error' => false,
                'message' => "Data Station Deleted."
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }
    /**-----------------------End Block For station Barcode Management----------------------*/
}