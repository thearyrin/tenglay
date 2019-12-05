<?php

namespace App\Http\Controllers\Admin\Setting\Group;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class UserGroupController extends Controller
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
        $data_check = $this->check_permission("SYS003", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $data_json = $this->call_api_by_parameter("webGetGroup", ['UserID' => $user_id]);

        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        }

        $data_encode = json_decode($data_json);

        $data['id'] = $data_encode->id;
        $data['list'] = $data_encode->data;
        $data['user_id'] = $user_id;
        $data['add_group'] = CheckPemission($this->check_permission("SYS003", "add"));
        $data['edit_group'] = CheckPemission($this->check_permission("SYS003", "edit"));
        $data['delete_group'] = CheckPemission($this->check_permission("SYS003", "delete"));

        return view('pages.backend.setting.usersgroup.show', compact("data"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $parmsadd = [
            "Name" => $request->name,
            "Des" => $request->description,
            "ShortName" => $request->short_name,
        ];

        $name = "webAddGroup";

        if ($request->id) {

            $name = "webEditGroup";

            $parmsadd = [
                "Name" => $request->name,
                "Des" => $request->description,
                "ID" => $request->id,
                "ShortName" => $request->short_name,
            ];
        }

        $call_api = $this->call_api_by_parameter($name, $parmsadd);

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
    public function delete($id)
    {
        $data_api = $this->call_api_by_parameter("webDeleteGroup", array("ID" => $id));

        if ($data_api) {
            $decode = json_decode($data_api);
            if ($decode->id) {
                return response()->json([
                    "message" => "Data User Deleted"
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

    //this function for get data group user
    public function list_group($id)
    {
        $call_group = $this->call_api_by_parameter("webGetGroup", ["UserID" => $id]);
        $user_group = $this->call_api_by_parameter("webGetUserGroup", ["UserID" => $id]);
        $delete_group = CheckPemission($this->check_permission("SYS005", "delete"));

        if ($call_group && $user_group) {

            $decode_group = json_decode($call_group);
            $decode_user_group = json_decode($user_group);

            return response()->json([
                'id_group' => $decode_group->id,
                'list_group' => $decode_group->data,
                'id_user_group' => $decode_user_group->id,
                'list_user_group' => $decode_user_group->data,
                'delete_group' => $delete_group
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection."
        ]);

    }

    //this function to save data user group
    public function save_group(Request $request)
    {

        $add_user_group = $this->call_api_by_parameter("webAddUserGroup",
            [
                "UserID" => $request->user_id,
                "GroupID" => $request->group_id
            ]
        );

        $delete_group = CheckPemission($this->check_permission("SYS005", "delete"));

        if ($add_user_group) {

            $user_group = json_decode($add_user_group);

            return response()->json([
                'id_user_group' => $user_group->id,
                'list_user_group' => $user_group->data,
                'msg' => $user_group->message,
                'delete' => $delete_group
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection."
        ]);
    }

    //this function for deleting user group
    public function delete_group($id)
    {
        $delete_user_group = $this->call_api_by_parameter("webDeleteUserGroup", ["ID" => $id]);

        if ($delete_user_group) {
            $decode_user_group = json_decode($delete_user_group);

            return response()->json([
                'user_group_id' => $decode_user_group->id,
                'msg' => $decode_user_group->message
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection."
        ]);
    }
}
