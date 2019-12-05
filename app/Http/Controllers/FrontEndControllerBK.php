<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/24/2018
 * Time: 2:48 PM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class FrontEndController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $rule_barcode = [
        'barcode' => "required"
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth')->except('logout');
    }

    //this function for showing search barcode
    public function ShowingBarcode(Request $request)
    {

        $user_id = Session::get("user_id");

        if (!Session::has("user_logged") || (Session::get("user_logged") == 0) || (Session::get("user_logged") == "")) {

            Session::forget("user_id");
            Session::forget("username");
            Session::forget("display_name");
            Session::forget("phone_number");
            Session::forget("user_logged");
            Session::forget("image");
            Session::forget("pin");

            return redirect('/')->withErrors("You are not login yet.");

        }

        $barcode = '';
        $message = '';
        $id_nozzle = '';
        $data_nozzle = '';
        $order_id = '';
        $id_order = '';
        $list_order = '';

        if ($request->isMethod('post')) {

            $validation = Validator::make($request->all(), $this->rule_barcode);

            if ($validation->fails()) {
                $message = "Please Enter or Scan Barcode ticket";
            }

            $barcode = $request->barcode;


            $get_nozzle = $this->call_api_by_parameter("webGetAllNozzle", ['UserID' => $user_id]);

            $decode_nozzle = json_decode($get_nozzle);

            $id_nozzle = $decode_nozzle->id;
            $data_nozzle = $decode_nozzle->data;
            $order_id = $request->order_id;

            if ($order_id == "") {

            } else {
                $call_order = $this->call_api_by_parameter("webGetOrderNumber", ["UserID" => $user_id]);
                if ($call_order) {
                    $decode_json_order = json_decode($call_order);
                    if ($decode_json_order->id) {
                        $order_id = $decode_json_order->data[0]->ID + 1;
                    } else {
                        $order_id = 1;
                    }
                } else {
                    return redirect('/')->with("Your are losing your connection");
                }
            }
        }

        $call_api_station = $this->call_api_by_parameter("webGetStation", array("UserID" => $user_id));

        if (($call_api_station)) {

            $encode_station = json_decode($call_api_station);
            $station_id = '';
            if ($encode_station->id) {
                $station_id = $encode_station->data[0]->StationNumber;
            }

            $tank_id = 0;
            $data_tank = [];

            if ($station_id != "") {
                $parm_noz = [
                    "UserGet" => $user_id,
                    "StationNumber" => $station_id,
                ];

                $call_api_fuel = $this->call_api_by_parameter("webGetFuelStatus", $parm_noz);

                $decode_fuel = json_decode($call_api_fuel);
                $tank_id = $decode_fuel->id;
                $data_tank = $decode_fuel->data;
            }

            $data['barcode'] = $barcode;
            $data['message'] = $message;
            $data['order_id'] = $order_id;

            $data['id'] = Session::get("user_id");
            $data['username'] = Session::get("username");
            $data['displayname'] = Session::get("display_name");
            $data['phone'] = Session::get("phone_number");
            $data['password'] = Session::get("pin");
            $data['photo'] = Session::get("image");

            $data['tank_id'] = $tank_id;
            $data['data_tank'] = $data_tank;

            $data['id_nozzle'] = $id_nozzle;
            $data['data_nozzle'] = $data_nozzle;

            return view('pages.frontend.index', compact('data'));
        }

        return redirect('/')->with("Your are losing your connection");

    }

    //this function for scanning barcode
    public function ScanningBarcode(Request $request)
    {

        if (!Session::has("user_logged") || (Session::get("user_logged") == 0) || (Session::get("user_logged") == "")) {

            Session::forget("user_id");
            Session::forget("username");
            Session::forget("display_name");
            Session::forget("phone_number");
            Session::forget("user_logged");
            Session::forget("image");
            Session::forget("pin");

            return redirect('/')->withErrors("You are not login yet.");

        }

        $array = array(
            "TicketID" => $request->ticket_id,
            "StationNumber" => $request->station_id,
            "StationIP" => $request->station_ip,
            "PumpNumber" => $request->pump_id,
            "NozzleNumber" => $request->nozzle_id,
            "Volume" => $request->volume,
            "UserID" => Session::get("user_id")
        );

        $call_api = $this->call_api_by_parameter("webSubmitTicketOrder", $array);
//        dd($call_api);

        if ($call_api) {
            $decode = json_decode($call_api);
            if ($decode->id) {
                $message = "Your ticket ticket was authorized.";
            } else {
                $message = $decode->message;
            }

            $data['smg'] = $message;
            return view('pages.frontend.result', compact('data'));
        }
        return redirect('/')->withErrors("You are losing your connection");

    }

    //this function for get method from url when use enter post to get
    public function GetAuthorize()
    {

        if (!Session::has("user_logged") || (Session::get("user_logged") == 0) || (Session::get("user_logged") == "")) {

            Session::forget("user_id");
            Session::forget("username");
            Session::forget("display_name");
            Session::forget("phone_number");
            Session::forget("user_logged");
            Session::forget("image");
            Session::forget("pin");

            return redirect('/')->withErrors("You are not login yet.");

        }

        return redirect('/home');
    }

    //this function is for get method scanning ticket barcode and nozzle barcode
    public function scan_ticket(Request $request)
    {

        if (!Session::has("user_logged") || (Session::get("user_logged") == 0) || (Session::get("user_logged") == "")) {

            Session::forget("user_id");
            Session::forget("username");
            Session::forget("display_name");
            Session::forget("phone_number");
            Session::forget("user_logged");
            Session::forget("image");
            Session::forget("pin");

            return redirect('/')->withErrors("You are not login yet.");

        }

        $user_id = Session::get("user_id");

        $call_api = $this->call_api_by_parameter("webGetTicketVoucher",
            array(
                "Barcode" => $request->barcode,
                "NozzleCode" => $request->nozzle_code,
                "UserID" => $user_id
            )
        );
//        dd($call_api);

        if ($call_api) {
            $encode = json_decode($call_api);

            if ($encode->id) {
                $data['list'] = $encode->data;
                $data['row'] = $encode->data[0];
                $data['id'] = $encode->id;
                $result = view('pages.frontend.result-scan', compact('data'))->render();
                return response()->json([
                    'error' => 0,
                    'data' => $result
                ]);
            }

            return response()->json([
                "error" => $encode->message
            ]);
        }
        return response()->json(['error' => "You are losing your connection."]);
    }


}
