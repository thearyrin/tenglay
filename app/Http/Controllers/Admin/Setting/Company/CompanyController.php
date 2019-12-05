<?php

namespace App\Http\Controllers\Admin\Setting\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class CompanyController extends Controller
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
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {

//        $data_check = $this->check_permission("SYS003");
        $check_per_com = $this->check_permission("SYS002", "view");

        if ($check_per_com == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($check_per_com);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }


        $user_id = Session::get("ID");

        $call_api = $this->call_api_by_parameter("webGetCompanyProfile", ["UserID" => $user_id]);
        if ($call_api) {
            $decode = json_decode($call_api);

            $id = 0;
            $com_id = '';
            $name = '';
            $short = '';
            $logo = '';

            if ($decode->id) {

                $id = $decode->id;
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

            $data['id'] = $id;
            $data['com_id'] = $com_id;
            $data['name'] = $name;
            $data['short'] = $short;
            $data['logo'] = $logo;

            $data['update_permission'] = CheckPemission($this->check_permission("SYS002", "edit"));;

            return view('pages.backend.setting.company.profile', compact('data'));
        }
        return redirect('/admin')->withErrors("Your are losing your connection");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        if (($request->file_name) && ($_FILES['upload_image']['error'] > 0)) {

            $files = array();

        } else if ($_FILES['upload_image']['size'] == '' && $_FILES['upload_image']['error'] > 0) {

            $name = "webUpdateCompanyProfileNoIMG";

            $parms = [
                "ID" => $request->company_id,
                "Name" => $request->company_name,
                "ShortCut" => $request->short_cut
            ];

            $call_api = $this->call_api_by_parameter($name, $parms);

        } else {

            $files = array($_FILES['upload_image']['tmp_name']);

            $name = "webUpdateCompanyProfile";

            $parms = [
                "ID" => $request->company_id,
                "Name" => $request->company_name,
                "ShortCut" => $request->short_cut
            ];

            $call_api = $this->call_api_by_parameter_post($name, $parms, $files);

        }

//        dd($call_api);

        return redirect('/admin/setting/company');

    }

}
