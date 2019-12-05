<?php

namespace App\Http\Controllers\Admin\Setting\RoundTrip;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RoundTripController extends Controller
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
        $data_check = $this->check_permission("SYS021", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $data_json = $this->call_api_by_parameter("webGetRoundTripSetting", ['UserID' => $user_id]);
        $call_round_trip = $this->call_api_by_parameter("webGetRoundTrip", ["UserID" => $user_id]);

        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        }

        $data_encode = json_decode($data_json);
        $round_decode = json_decode($call_round_trip);

        $data['id'] = $data_encode->id;
        $data['list'] = $data_encode->data;
        $data['user_id'] = $user_id;
        $data['id_round'] = $round_decode->id;
        $data['list_round'] = $round_decode->data;
        $data['add'] = CheckPemission($this->check_permission("SYS021", 'add'));
        $data['edit'] = CheckPemission($this->check_permission("SYS021", 'edit'));
        $data['delete'] = CheckPemission($this->check_permission("SYS021", 'delete'));

        return view('pages.backend.setting.roundtrip.show', compact("data"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $type = '';
        if ($request->round_trip_type == 1) {
            $type = "Empty";
        } else if ($request->round_trip_type == 2) {
            $type = "Laden";
        } else if ($request->round_trip_type == 3) {
            $type = "Only Truck";
        } else if ($request->round_trip_type == 4) {
            $type = "Other";
        }
        $parmsadd = [
            "UserID" => Session::get("ID"),
            "Port" => $request->port,
            "Amount" => $request->amount,
            "RoundType" => $type,
            "RoundTypeID" => $request->round_trip_type,
        ];

        $name = "webAddRoundTripSetting";

        if ($request->id) {

            $name = "webEditRoundTripSetting";

            $parmsadd = [
                "ID" => $request->id,
                "UserID" => Session::get("ID"),
                "Port" => $request->port,
                "Amount" => $request->amount,
                "RoundType" => $type,
                "RoundTypeID" => $request->round_trip_type,
            ];
        }

//        dd($parmsadd);
        $call_api = $this->call_api_by_parameter($name, $parmsadd);
//        dd($call_api);

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
//        dd($request->id);
        $data_api = $this->call_api_by_parameter("webDeleteRoundTripSetting", array("ID" => $request->id));

        if ($data_api) {
            $decode = json_decode($data_api);
            dd($decode);
            if ($decode->id) {
                return response()->json([
                    "message" => "Data setting round trip return Deleted"
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
}
