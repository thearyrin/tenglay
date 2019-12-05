<?php

namespace App\Http\Controllers\Admin\Setting\AccountBalance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AccountBalanceController extends Controller
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

        $data_check = $this->check_permission("SYS041", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $data_json = $this->call_api_by_parameter("webGetAccountBalance", ['UserID' => $user_id]);

        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        }

        $data_encode = json_decode($data_json);

        $data['id'] = $data_encode->id;
        $data['list'] = $data_encode->data;
        $data['user_id'] = $user_id;

        $data['add'] = CheckPemission($this->check_permission("SYS041", 'add'));
        $data['edit'] = CheckPemission($this->check_permission("SYS041", 'edit'));

        return view('pages.backend.setting.account_balance.show', compact("data"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {


        $name = "webTopupAccountBalance";

        $parmsadd = [
            "UserID" => $request->user_id,
            "Description" => $request->description,
            "AmountDollar" => $request->amount_dollar,
            "AmountKHR" => $request->amount_khr,
            "CashAdvancedNo" => $request->cash_number,
        ];

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $name = "webEditAccountBalance";

        $parmsadd = [
            "UserID" => $request->user_id,
            "NameAccount" => $request->name_acc,
            "ID" => $request->id_account,
        ];

//        dd($parmsadd);

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
}
