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

        $data_check = $this->check_permission_front("SYS033", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.frontend.message');
        }

        $barcode = '';
        $message = '';
        $id_nozzle = '';
        $data_nozzle = '';

        $call_api_station = $this->call_api_by_parameter("webGetStation", array("UserID" => $user_id));

        $call_list = $this->call_api_by_parameter("webGetTicketListRescan", ['UserID' => $user_id]);
        $id_list = '';
        $data_list = '';

        if (($call_api_station) && ($call_list)) {

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

                if ($request->isMethod('post')) {

                    $validation = Validator::make($request->all(), $this->rule_barcode);

                    if ($validation->fails()) {
                        $message = "Please Enter or Scan Barcode ticket";
                    }

                    $barcode = $request->barcode;
                }

                $get_nozzle = $this->call_api_by_parameter("webGetAllNozzle", ['UserID' => $user_id, 'StationNumber' => $station_id]);

                $decode_nozzle = json_decode($get_nozzle);

                $id_nozzle = $decode_nozzle->id;
                $data_nozzle = $decode_nozzle->data;

            }

            $decode_list = json_decode($call_list);
            $id_list = $decode_list->id;
            $data_list = $decode_list->data;

            $error = 0;
            $msg = '';

            if ($barcode) {

                $get_status = $this->call_api_by_parameter("webCheckTicketStatus", ['Barcode' => $barcode]);
                $decode_status = json_decode($get_status);
                $error = $decode_status->data[0]->id;
                $msg = $decode_status->data[0]->message;
            } else {
                $barcode = "";
            }

            $data['barcode'] = $barcode;
            $data['message'] = $message;

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

            $data['id_list'] = $id_list;
            $data['data_list'] = $data_list;

            $data['error'] = $error;
            $data['msg'] = $msg;
            $data['add'] = CheckPemission($this->check_permission_front("SYS033", 'add'));

            return view('pages.frontend.index', compact('data'));
        }

        return redirect('/')->with("Your are losing your connection");

    }

    //this function for checking barcode
    public function CheckingBarcode(Request $request)
    {
        $user_id = Session::get("user_id");

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

                if ($request->isMethod('post')) {

                    $validation = Validator::make($request->all(), $this->rule_barcode);

                    if ($validation->fails()) {
                        $message = "Please Enter or Scan Barcode ticket";
                    }

                    $barcode = $request->barcode;
                }

                $get_nozzle = $this->call_api_by_parameter("webGetAllNozzle", ['UserID' => $user_id, 'StationNumber' => $station_id]);

                $decode_nozzle = json_decode($get_nozzle);

                $id_nozzle = $decode_nozzle->id;
                $data_nozzle = $decode_nozzle->data;

            }
        }

        $data['id_nozzle'] = $id_nozzle;
        $data['data_nozzle'] = $data_nozzle;
        $nozzle_status = view('pages.frontend.nozzle', compact('data'))->render();

        if ($request->barcode) {

            $get_status = $this->call_api_by_parameter("webCheckTicketStatus", ['Barcode' => $request->barcode]);
            $decode_status = json_decode($get_status);

            $error = $decode_status->data[0]->id;
            $msg = $decode_status->data[0]->message;

            $result_id = 0;
            $result_data = '';

            if ($error) {

                $get_result = $this->call_api_by_parameter("webGetTicketDataByBarcode", ['Barcode' => $request->barcode]);
                $decode_result = json_decode($get_result);
                $result_id = $decode_result->id;

                if ($result_id) {
                    $result_data = $decode_result->data[0];
                }
            }

            return response()->json([
                'error' => $error,
                'msg' => $msg,
                'nozzle_data' => $nozzle_status,
                'result_id' => $result_id,
                'result_data' => $result_data,
            ]);
        }
    }

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

        $count_ticket = count($request->ticket_id);

        $ticket_id = '';

        $i = 0;

        if ($count_ticket > 0) {
            foreach ($request->ticket_id as $id) {

                $i++;

                $coma = ",";
                if ($count_ticket == $i) {
                    $coma = '';
                }

                $ticket_id .= $id . $coma;
            }

        }

        $array = array(
            "TicketID" => $ticket_id,
            "StationNumber" => $request->station_id,
            "StationIP" => $request->station_ip,
            "PumpNumber" => $request->pump_id,
            "NozzleNumber" => $request->nozzle_id,
            "Volume" => $request->total_volume,
            "UserID" => Session::get("user_id")
        );

        $call_api = $this->call_api_by_parameter("webSubmitTicketOrder", $array);

        if ($call_api) {
            $decode = json_decode($call_api);
            if ($decode->id) {
                $message = "Your ticket was authorized.";
            } else {
                $message = $decode->message;
            }

            $data['smg'] = $message;
            $data['error'] = $decode->error;
            $data['station_ip'] = $request->station_ip;
            $data['pump_id'] = $request->pump_id;

            $result = view('pages.frontend.result', compact('data'))->render();
            return response()->json([
                'error' => 0,
                'data' => $result
            ]);
        }

        return response()->json([
            'error' => 0,
            'data' => 'You are losing your connection'
        ]);
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

        $count_ticket = count($request->ticket_id);
        $ticket_id = '';

        $i = 0;

        if ($count_ticket > 0) {
            foreach ($request->ticket_id as $id) {

                $i++;

                $coma = ",";
                if ($count_ticket == $i) {
                    $coma = '';
                }

                $ticket_id .= $id . $coma;
            }

        }


        $call_api = $this->call_api_by_parameter("webGetTicketAvailable", [
            "NozzleCode" => $request->nozzle_code_num,
            "TicketID" => $ticket_id,
            "UserID" => $user_id
        ]);

        if ($call_api) {

            $encode = json_decode($call_api);

            if ($encode->id) {

                $row = $encode->data[0];

                $count_ticket = count($encode->data);

                $ticket_id = '';
                $i = 0;
                $total = 0;

                if ($count_ticket > 0) {

                    foreach ($encode->data as $item) {

                        $i++;

                        $coma = ",";
                        if ($count_ticket == $i) {
                            $coma = '';
                        }


                        $ticket_id .= $item->ID . $coma;

                        $total += $item->TotalAmountFuel;
                    }

                }

                $array = array(
                    "TicketID" => $ticket_id,
                    "StationNumber" => $row->StationNumber,
                    "StationIP" => $row->StationIP,
                    "PumpNumber" => $row->PumpNumber,
                    "NozzleNumber" => $row->NozzleNumber,
                    "Volume" => $total,
                    "UserID" => $user_id
                );

                $call_api = $this->call_api_by_parameter("webSubmitTicketOrder", $array);
                
                if ($call_api) {

                    $decode = json_decode($call_api);

                    if ($decode->id) {
                        $message = "Your ticket was authorized.";
                    } else {
                        $message = $decode->message;
                    }

                    $data['smg'] = $message;
                    $data['error'] = $decode->error;
                    $data['station_ip'] = $row->StationIP;
                    $data['pump_id'] = $row->PumpNumber;

                    $result = view('pages.frontend.result', compact('data'))->render();

                    return response()->json([
                        'error' => 0,
                        'data' => $result
                    ]);
                }
            }

            return response()->json([
                "error" => $encode->message
            ]);
        }
        return response()->json(['error' => "You are losing your connection."]);
    }

    public function Deauthorize(Request $request)
    {
        $call_api = $this->call_api_by_parameter("WebPumpDeauthorize", [
            "StationIP" => $request->station_ip,
            "PumpNumber" => $request->pump_id
        ]);

        if ($call_api) {

            $decode = json_decode($call_api);
            if ($decode->id) {
                return redirect('/');
            } else {
                return redirect('frontend/authorize')->withErrors("Pump note deauthorize yet.");
            }
        }

        return redirect('/')->withErrors("You are losing your connection");
    }

    public function RescanList(Request $request)
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

        $data_check = $this->check_permission_front("SYS033", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.frontend.message');
        }

        $id_nozzle = 0;
        $data_nozzle = [];

        $id_list = '';
        $data_list = '';

        $call_api_station = $this->call_api_by_parameter("webGetStation", array("UserID" => $user_id));

        $call_list = $this->call_api_by_parameter("webGetTicketListRescan", ['UserID' => $user_id]);


        if (($call_api_station) && ($call_list)) {

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

                $get_nozzle = $this->call_api_by_parameter("webGetAllNozzle", ['UserID' => $user_id, 'StationNumber' => $station_id]);

                $decode_nozzle = json_decode($get_nozzle);

                $id_nozzle = $decode_nozzle->id;
                $data_nozzle = $decode_nozzle->data;

            }

            $decode_list = json_decode($call_list);
            $id_list = $decode_list->id;
            $data_list = $decode_list->data;

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

            $data['id_list'] = $id_list;
            $data['data_list'] = $data_list;

            $data['add'] = CheckPemission($this->check_permission_front("SYS033", 'add'));

            return view('pages.frontend.rescan', compact('data'));
        }

        return redirect('/')->with("Your are losing your connection");
    }

    public function RescanTicket(Request $request)
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

        $error = 0;

        $check_ticket_status = $this->call_api_by_parameter("webCheckTicketRescanStatus", ["TicketID" => $request->ticket_id]);

        if ($check_ticket_status) {

            $decode_ticket_status = json_decode($check_ticket_status);

            if ($decode_ticket_status->id) {

                $call_api = $this->call_api_by_parameter("webGetTicketAvailable", [
                    "NozzleCode" => $request->nozzle_code,
                    "TicketID" => $request->ticket_id,
                    "UserID" => $user_id
                ]);

                if (($call_api)) {

                    $encode = json_decode($call_api);

                    if ($encode->id) {

                        $row = $encode->data[0];

                        $count_ticket = count($encode->data);

                        $ticket_id = '';
                        $i = 0;
                        $total = 0;

                        if ($count_ticket > 0) {

                            foreach ($encode->data as $item) {

                                $i++;

                                $coma = ",";
                                if ($count_ticket == $i) {
                                    $coma = '';
                                }


                                $ticket_id .= $item->ID . $coma;

                                $total += $item->TotalAmountFuel;
                            }

                        }

                        $array = array(
                            "TicketID" => $ticket_id,
                            "StationNumber" => $row->StationNumber,
                            "StationIP" => $row->StationIP,
                            "PumpNumber" => $row->PumpNumber,
                            "NozzleNumber" => $row->NozzleNumber,
                            "Volume" => $total,
                            "UserID" => $user_id
                        );


                        $call_api = $this->call_api_by_parameter("webSubmitTicketOrder", $array);

                        if ($call_api) {

                            $decode = json_decode($call_api);

                            if ($decode->id) {
                                $message = "Your ticket was authorized.";
                            } else {
                                $message = $decode->message;
                            }

                            $data['smg'] = $message;
                            $data['error'] = $decode->error;
                            $data['station_ip'] = $row->StationIP;
                            $data['pump_id'] = $row->PumpNumber;

                            $result = view('pages.frontend.result', compact('data'))->render();

                            return response()->json([
                                'error' => 0,
                                'data' => $result,
                                'data_pump' => $decode->error
                            ]);
                        }
                    }

                    return response()->json([
                        "error" => $encode->message,
                        'data_pump' => $error
                    ]);
                }

                return response()->json(['error' => "You are losing your connection.", 'data_pump' => $error]);

            } else {
                return response()->json([
                    "error" => $decode_ticket_status->message,
                    'data_pump' => $error
                ]);
            }

        }

        return response()->json(['error' => "You are losing your connection.", 'data_pump' => $error]);
    }

}
