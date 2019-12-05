<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/25/2018
 * Time: 11:29 AM
 */

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BackendController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $check_rule_login = [
        "username" => "required",
        "password" => "required"
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    //this function for login form
    public function ShowLoginForm()
    {

        $call_api = $this->call_api_by_parameter("webGetCompanyProfile", ["UserID" => ""]);

//        if ($call_api) {
        $decode = json_decode($call_api);
//            dd($decode);
        $com_id = '';
        $name = '';
        $short = '';
        $logo = '';

        if ($decode->id) {

            $row = $decode->data[0];
            $com_id = $row->ID;
            $name = $row->Name;
            $short = $row->ShortCut;
            $logo = $row->Logo;
        }

        Session::put("com_id", $com_id);
        Session::put("name", $name);
        Session::put("short", $short);
        Session::put("logo", $logo);
//        }

        if (!Session::has("is_logged") || (Session::get("is_logged") == 0) || (Session::get("is_logged") == "")) {

            Session::forget("is_logged");
            Session::forget("ID");
            Session::forget("UserName");
            Session::forget("PhoneNumber");
            Session::forget("DisplayName");
            Session::forget("photo");
            Session::forget("password");
            Session::forget("group_id");
            Session::forget("group_name");
            Session::forget("StatusDefault");

            return view("pages.backend.login");

        } else {

            $call_api = $this->call_api_by_parameter("webCheckMenu", ["UserID" => Session::get("ID")]);

            if ($call_api == false) {
                return Redirect::back()->withErrors("You are losing your connection.");
            }

            $decode = json_decode($call_api);

            if ($decode->id == 0) {
                return redirect('admin/welcome');
            }

            return redirect('admin/ticket/create');
        }

    }

    //this function is for do login
    public function DoLogin(Request $request)
    {

        $validation = Validator::make($request->all(), $this->check_rule_login);

        if ($validation->fails()) {
            return Redirect::back()->withErrors("Please Enter Your Username and Password");
        }

        $parm = [
            "UserName" => $request->username,
            "Password" => $request->password
        ];

        $call_api = $this->call_api_by_parameter("webCheckUserNamePassword", $parm);

//        dd($call_api);
        if ($call_api == false) {
            return Redirect::back()->withErrors("You are losing your connection.");
        }

        $decode = json_decode($call_api);

        if ($decode->id == 0) {
            return Redirect::back()->withErrors($decode->message);
        }

        $row = $decode->data[0];
//        dd($row);

        $request->session()->put('is_logged', $row->ID);

        $request->session()->put('ID', $row->ID);
        $request->session()->put('UserName', $row->UserName);
        $request->session()->put('DisplayName', $row->DisplayName);
        $request->session()->put('PhoneNumber', $row->PhoneNumber);
        $request->session()->put('password', $row->Password);
        $request->session()->put('photo', $row->PhotoBase64);
        $request->session()->put('group_id', $row->GroupID);
        $request->session()->put('group_name', $row->ShortName);
        $request->session()->put('StatusDefault', $row->StatusDefault);

//        $data_check = $this->check_permission("SYS002");
//
//        if ($data_check == false) {
//            return redirect('/admin')->withErrors("You are losing your connection.");
//        }
//
//        $check_menu = CheckPemission($data_check);
//
//        if ($check_menu == false) {

//        Session::forget("ID");
//        Session::forget("UserName");
//        Session::forget("DisplayName");
//        Session::forget("PhoneNumber");
//        Session::forget("is_logged");
//        Session::forget("photo");
//        Session::forget("password");

//        return redirect('/admin')->withErrors("You have no permission to get in");
//        }

        $call_api = $this->call_api_by_parameter("webCheckMenu", ["UserID" => Session::get("ID")]);

        if ($call_api == false) {
            return Redirect::back()->withErrors("You are losing your connection.");
        }

        $decode = json_decode($call_api);

        if ($decode->id == 0) {
            return redirect('admin/welcome');
        }

        return redirect('admin/ticket/create');

    }


    public function logout()
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

        return redirect('admin/');
    }

    public function welcome(){
        return view('welcome');
    }
}