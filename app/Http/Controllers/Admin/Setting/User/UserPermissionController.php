<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 6/7/2018
 * Time: 2:09 PM
 */

namespace App\Http\Controllers\Admin\Setting\User;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserPermissionController extends Controller
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

    //this function for getting users
    public function show()
    {

        $user_id = Session::get("ID");
        $call_api = $this->call_api_by_parameter("webGetUser", ["UserGet" => $user_id]);

        if ($call_api) {

            $decode = json_decode($call_api);
            $id = $decode->id;
            $data = $decode->data;
            return view("pages.setting.permission.show", compact('id', 'data', 'user_id'));
        }
        return redirect("/admin")->withErrors("You are losing your connection");

    }

    //this function for getting data users authorize
    public function get(Request $request)
    {

        $parms = ["UserGet" => $request->logged_user_id, "UserID" => $request->current_user_id];

//        $call_api = $this->call_api_by_parameter("getProcessUnauthorized", $parms);
//        $call_api_user = $this->call_api_by_parameter("getProcessAuthorized", $parms);
        $call_menu = $this->call_api_by_parameter("webGetMenu", ["UserID" => $request->current_user_id]);
//dd($call_menu);

        if ($call_menu) {

//            $decode = json_decode($call_api);
            $menu = json_decode($call_menu);

            return response()->json([

                "id" => $menu->id,
                "data" => $menu->data,
                'msg' => $menu->message,
//                'user_id' => $decode_user->id,
//                'user_data' => $decode_user->data
            ]);
        }

        return response()->json([
            "error" => "You are losing your connection"
        ]);
    }

    //this function for saving data users process
    public function save(Request $request)
    {

//        $parms_check = [
//            "UserCheck" => $request->user_logged,
//            'UserID' => $request->current_user,
//            "ProcessCode" => $request->process_code
//        ];
//
//        $call_api_check = $this->call_api_by_parameter("checkUserProcess", $parms_check);
//
//        if ($call_api_check) {
//
//            $deconde_check = json_decode($call_api_check);
//
//            if ($deconde_check->id) {
//                return response([
//                    "error" => "That process code already exist."
//                ]);
//            }
//
//            $parm_add = ["UserAdd" => $request->user_logged, "UserID" => $request->current_user, "ProcessCode" => $request->process_code];
//            $call_api_add = $this->call_api_by_parameter("addUserProcess", $parm_add);
//
//            if ($call_api_add) {
//
//                $decode_add = json_decode($call_api_add);
//
//                return response()->json([
//                    "id" => $decode_add->id,
//                    "data" => $decode_add->data,
//                    "msg" => $decode_add->message
//                ]);
//            }
//            return response()->json([
//                "error" => "You are losing your connection"
//            ]);
//
//        }
//
//        return response()->json([
//            "error" => "You are losing your connection"
//        ]);

        $logged_id = $request->logged_id;
        $current_id = $request->current_id;

        foreach ($request->menu_id as $menu) {

            if (($request->has("view" . $menu) && !empty($request->view . $menu))) {
                $view_page = 1;
            } else {
                $view_page = 0;
            }

            if (($request->has("add" . $menu) && !empty($request->add . $menu))) {
                $add_page = 1;
            } else {
                $add_page = 0;
            }

            if (($request->has("edit" . $menu) && !empty($request->edit . $menu))) {
                $edit_page = 1;
            } else {
                $edit_page = 0;
            }

            if (($request->has("delete" . $menu) && !empty($request->delete . $menu))) {
                $delete_page = 1;
            } else {
                $delete_page = 0;
            }

            $parms_add = [
                "LoggedID" => $logged_id,
                "CurrentID" => $current_id,
                "MenuID" => $menu,
                "ViewPage" => $view_page,
                "AddPage" => $add_page,
                "EditPage" => $edit_page,
                "DeletePage" => $delete_page
            ];

//            dd($parms_add);
            $insert_menu_user = $this->call_api_by_parameter("webAddUserMenu", $parms_add);
//            dd($insert_menu_user);
        }

        return response()->json([
            "error" => 0,
            "msg" => "Permission Added"
        ]);

    }

    //this function for deleting data users authorize
    public function delete(Request $request)
    {

        $parm_delete = [
            "UserDelete" => $request->user_logged,
            "UserID" => $request->current_user,
            "ProcessCode" => $request->process_code
        ];

        $call_api_delete = $this->call_api_by_parameter("deleteUserProcess", $parm_delete);

        if ($call_api_delete) {

            $decode_add = json_decode($call_api_delete);

            return response()->json([
                "id" => $decode_add->id,
                "data" => $decode_add->data,
                "msg" => $decode_add->message
            ]);
        }

        return response()->json([
            "error" => "You are losing your connection"
        ]);
    }
}