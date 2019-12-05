<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class LoginFrontController extends Controller
{

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

    //this function for show form login
    public function ShowLogin()
    {

//        dd("come here login");
        $call_api = $this->call_api_by_parameter("webGetCompanyProfile", ["UserID" => ""]);

        $decode = json_decode($call_api);
//        dd($decode);
        $com_id = '';
        $name = '';
        $short = '';
        $logo = '';
//dd($decode);
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

        if (!Session::has("user_logged") || (Session::get("user_logged") == 0) || (Session::get("user_logged") == "")) {

            Session::forget("user_id");
            Session::forget("username");
            Session::forget("display_name");
            Session::forget("phone_number");
            Session::forget("user_logged");
            Session::forget("image");
            Session::forget("pin");

            return view("pages.frontend.login");

        } else {
            return redirect('home');
        }
    }

    //this function for doing login
    public function DoLogin(Request $request)
    {

//        dd("come here dologin");
        $validation = Validator::make($request->all(), $this->check_rule_login);

        if ($validation->fails()) {
            return Redirect::back()->withErrors("Please Enter Your Username and Password");
        }

        $parm = [
            "UserName" => $request->username,
            "Password" => $request->password
        ];

        $call_api = $this->call_api_by_parameter("webCheckUserNamePassword", $parm);

        if ($call_api == false) {
            return Redirect::back()->withErrors("You are losing your connection.");
        }

        $decode = json_decode($call_api);

        if ($decode->id == 0) {
            return Redirect::back()->withErrors($decode->message);
        }

        $row = $decode->data[0];

        $request->session()->put('user_logged', $row->ID);

        $request->session()->put('user_id', $row->ID);
        $request->session()->put('username', $row->UserName);
        $request->session()->put('display_name', $row->DisplayName);
        $request->session()->put('phone_number', $row->PhoneNumber);
        $request->session()->put('pin', $row->Password);
        $request->session()->put('image', $row->PhotoBase64);

//        $data_check = $this->check_permission_front("SYS033", "view");
//
//        if ($data_check == false) {
//            return redirect('/')->withErrors("You are losing your connection.");
//        }
//
//        $check_menu = CheckPemission($data_check);
//
//        if ($check_menu == false) {
//
//            Session::forget("user_id");
//            Session::forget("username");
//            Session::forget("display_name");
//            Session::forget("phone_number");
//            Session::forget("user_logged");
//            Session::forget("image");
//            Session::forget("pin");
//
//            return redirect('/')->withErrors("You have no permission to get in");
//        }

        return redirect('/home');
    }

    //this function for logout function
    public function logout()
    {

        Session::forget("user_id");
        Session::forget("username");
        Session::forget("display_name");
        Session::forget("phone_number");
        Session::forget("user_logged");
        Session::forget("image");
        Session::forget("pin");

        return redirect('/');
    }
}
