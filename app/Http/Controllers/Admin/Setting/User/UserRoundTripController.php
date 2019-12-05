<?php

namespace App\Http\Controllers\Admin\Setting\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class UserRoundTripController extends Controller
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
    public function index($id)
    {
        $user_id = Session::get("ID");

        $call_round_trip = $this->call_api_by_parameter("webGetRoundTrip", ["UserID" => $user_id]);
        $user_round = $this->call_api_by_parameter("webGetUserRoundTrip", ["UserID" => $id]);
        $delete = CheckPemission($this->check_permission("SYS007", "delete"));

        if ($call_round_trip && $user_round) {

            $decode_round_trip = json_decode($call_round_trip);
            $user_round = json_decode($user_round);

            return response()->json([
                'id_round' => $decode_round_trip->id,
                'list_round' => $decode_round_trip->data,
                'id_user_round' => $user_round->id,
                'list_user_round' => $user_round->data,
                'delete' => $delete
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
    public function save(Request $request)
    {

        $add_user_trip = $this->call_api_by_parameter("webAddUserRoundTrip", ["UserID" => $request->user_id, "RoundTrip" => $request->round_trip]);
        $delete = CheckPemission($this->check_permission("SYS007", "delete"));

        if ($add_user_trip) {

            $round_trip = json_decode($add_user_trip);

            return response()->json([
                'id_round' => $round_trip->id,
                'list_round' => $round_trip->data,
                'msg' => $round_trip->message,
                'delete' => $delete
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
    public function delete($id)
    {
        $add_user_trip = $this->call_api_by_parameter("webDeleteUserRoundTrip", ["ID" => $id]);

        if ($add_user_trip) {
            $round_trip = json_decode($add_user_trip);

            return response()->json([
                'id_round' => $round_trip->id,
                'msg' => $round_trip->message
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection."
        ]);
    }
}
