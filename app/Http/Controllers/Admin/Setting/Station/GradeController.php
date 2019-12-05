<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 5/17/2018
 * Time: 5:00 PM
 */

namespace App\Http\Controllers\Admin\Setting\Station;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except("logout");
    }

    /**-----------------------End Block For Grade Management----------------------*/
    //this function is for get grade list
    public function get_grade(Request $request)
    {

        $call_api = $this->call_api_by_parameter("webGetGrade", ['UserID' => $request->user_id, "StationNumber" => $request->id]);
        $delete = CheckPemission($this->check_permission("SYS009", 'delete'));

        if ($call_api) {
            $decode = json_decode($call_api);
            return response()->json([
                'error' => false,
                'id' => $decode->id,
                'data' => $decode->data,
                'delete' => $delete
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function for deleting grade
    public function delete_grade(Request $request)
    {

        $call_api = $this->call_api_by_parameter("webDeleteGrade", ['UserID' => $request->user_id, "GradeID" => $request->id]);

        if ($call_api) {

            return response()->json([
                'error' => false,
                'message' => "The Grade Deleted."
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function for saving data grade
    public function save_grade(Request $request)
    {

        $parm = ['UserID' => $request->user_id, "StationNumber" => $request->station_id, "GradeNumber" => $request->grade_number, "GradeDescription" => $request->des, "GradeColor" => $request->grade_color];

        $call_api = $this->call_api_by_parameter("webAddGrade", $parm);

        $delete = CheckPemission($this->check_permission("SYS009", 'delete'));

        if ($call_api) {

            $decode = json_decode($call_api);

            if ($decode->id) {
                return response()->json([
                    'error' => false,
                    'data' => $decode->data[0],
                    'smg' => "Data Grade Saved",
                    'delete' => $delete
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
    /**-----------------------End Block For Grade Management----------------------*/
}