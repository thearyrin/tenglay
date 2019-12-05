<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 5/17/2018
 * Time: 5:12 PM
 */

namespace App\Http\Controllers\Admin\Setting\User;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
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


    /** ---------------------Start Route For User block--------------------------------------------*/
    //this function is for show users data
    public function show()
    {

        $data_check = $this->check_permission("SYS004", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $data_json = $this->call_api_by_parameter("webGetUser", ['UserGet' => $user_id]);
        $group_json = $this->call_api_by_parameter("webGetGroup", ['UserID' => $user_id]);

        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        }

//        dd($data_json);

        $data_encode = json_decode($data_json);
        $group_decode = json_decode($group_json);

        $data['id'] = $data_encode->id;
        $data['user_list'] = $data_encode->data;
        $data['user_id'] = $user_id;
        $data['group_id'] = $group_decode->id;
        $data['group_list'] = $group_decode->data;

        $data['check_permission'] = CheckPemission($this->check_permission("SYS006", 'view'));
        $data['add_permission'] = CheckPemission($this->check_permission("SYS006", 'add'));

        $data['check_round'] = CheckPemission($this->check_permission("SYS007", "view"));
        $data['add_round'] = CheckPemission($this->check_permission("SYS007", "add"));

        $data['check_group'] = CheckPemission($this->check_permission("SYS005", "view"));
        $data['add_group'] = CheckPemission($this->check_permission("SYS005", "add"));

        $data['add'] = CheckPemission($this->check_permission("SYS004", "add"));
        $data['edit'] = CheckPemission($this->check_permission("SYS004", "edit"));
        $data['delete'] = CheckPemission($this->check_permission("SYS004", "delete"));

        return view('pages.backend.setting.users.show', compact("data"));
    }

    //this function for save data users
    public function save(Request $request)
    {

        if (($request->file_name) && ($_FILES['user_photo']['error'] > 0)) {
            $files = array();
        } else if ($_FILES['user_photo']['size'] == '' && $_FILES['user_photo']['error'] > 0) {
            $files = array(public_path('images/blank.png'));
        } else {
            $files = array($_FILES['user_photo']['tmp_name']);
        }

        $parmsadd = [
            "UserAdd" => $request->user_id,
            "UserName" => $request->username,
            "Password" => $request->password,
            "DisplayName" => $request->displayname,
            "PhoneNumber" => $request->phone,
            "GroupID" => $request->list_group,
        ];

        $name = "webAddUser";

        if ($request->id) {

            $name = "webEditUser";

            $parmsadd = [
                "UserName" => $request->username,
                "Password" => $request->password,
                "DisplayName" => $request->displayname,
                "PhoneNumber" => $request->phone,
                "UserEdit" => $request->user_id,
                "UserID" => $request->id,
                "Status" => $request->status,
                "GroupID" => $request->list_group,
            ];
        }

        $call_api = $this->call_api_by_parameter_post($name, $parmsadd, $files);
        if ($call_api) {

            $data_encode = json_decode($call_api);
            if ($data_encode->id) {
                $user_id = $data_encode->data[0]->ID;
                $group_id = $request->list_group;

                $add_user_group = $this->call_api_by_parameter("webAddUserGroup", ["UserID" => $user_id, "GroupID" => $group_id]);

                return response()->json([
                    'data' => $data_encode->data[0],
                    'image' => ""
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

    //this function is for delete users
    public function delete(Request $request)
    {

        $data_api = $this->call_api_by_parameter("webDeleteUser", array("UserDelete" => $request->user_id, "UserID" => $request->id));

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

    //This function is for users profile
    public function profile_user()
    {
        $data['id'] = Session::get("ID");
        $data['username'] = Session::get("UserName");
        $data['displayname'] = Session::get("DisplayName");
        $data['phone'] = Session::get("PhoneNumber");
        $data['password'] = Session::get("password");
        $data['photo'] = Session::get("photo");
        return view("pages.backend.setting.users.profile", compact('data'));
    }

    /**------------This is block for users profile----------------**/

    //this function is for upload image
    public function upload(Request $request)
    {

        if (($request->file_name) && ($_FILES['upload_image']['error'] > 0)) {
            $files = array();
        } else if (($_FILES['upload_image']['size'] == 0) && ($_FILES['upload_image']['error'] > 0)) {

            $files = array(public_path('images/blank.png'));
        } else {

            $files = array($_FILES['upload_image']['tmp_name']);
        }

        $name = "webEditUser";

        $parmsadd = [
            "UserName" => $request->username,
            "Password" => $request->password,
            "DisplayName" => $request->displayname,
            "PhoneNumber" => $request->phone,
            "UserEdit" => $request->user_id,
            "UserID" => $request->user_id,
            "Status" => 1,
            "GroupID" => Session::get("group_id")

        ];

        $call_api = $this->call_api_by_parameter_post($name, $parmsadd, $files);

        if ($call_api) {

            $data_encode = json_decode($call_api);

            if ($data_encode->id) {

                $data = $data_encode->data[0];

                if ($request->password != Session::get("password")) {
                    return $this->change_profile();
                }

                Session::put("is_logged", $data_encode->id);
                Session::put("ID", $data->ID);
                Session::put("UserName", $data->UserName);
                Session::put("PhoneNumber", $data->PhoneNumber);
                Session::put("photo", $data->ImageBase64);
                Session::put("password", $data->Password);

                return redirect('admin/setting/users/profile');
            }

            return redirect('admin/s/setting/users/profile')->withErrors($data_encode->message);
        }

        return redirect('/admin')->withErrors("You are losing your connection.");

    }

    //this function for logout after users edit their profile
    public function change_profile()
    {

        Session::forget("ID");
        Session::forget("UserName");
        Session::forget("DisplayName");
        Session::forget("PhoneNumber");
        Session::forget("is_logged");
        Session::forget("photo");
        Session::forget("password");
        Session::forget("group_id");
        Session::forget("group_name");
        Session::forget("StatusDefault");

        return redirect("/admin")->withErrors("Your password or username has been changed successfully.");
    }

}