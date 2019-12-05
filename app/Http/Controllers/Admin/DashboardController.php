<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('logout');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $check_per_dash = $this->check_permission("SYS001", "view");

        if ($check_per_dash == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($check_per_dash);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $daily = date("d-M-Y");
        $day = date("d");
        $week_num = date("W") - date("W", strtotime(date("Y-m-01"))) + 1;
        $year = date("Y");
        $month_name = date("F");
        $month_num = date("m");

        //ticket
        $call_dashboard_daily = $this->call_api_by_parameter("webGetDashboardDaily", ["Daily" => $daily]);
        $call_dashboard_weekly = $this->call_api_by_parameter("webGetDashboardWeekly",
            ["WeekNumber" => $week_num, "MonthNumber" => $month_num, "YearNumber" => $year]);
        $call_dashboard_monthly = $this->call_api_by_parameter("webGetDashboardMonthly", [
            "Month" => $month_num,
            "Year" => $year
        ]);

        //ticket
        $decode_dashboard_daily = json_decode($call_dashboard_daily);
        $decode_dashboard_weekly = json_decode($call_dashboard_weekly);
        $decode_dashboard_monthly = json_decode($call_dashboard_monthly);


        $data['day'] = $day;
        $data['daily'] = $daily;
        $data['week_num'] = $week_num;
        $data['year'] = $year;
        $data['month_name'] = $month_name;

        //ticket
        $data['daily_dashboard_id'] = $decode_dashboard_daily->id;
        $data['daily_dashboard_list'] = $decode_dashboard_daily->data;
        $data['week_dashboard_id'] = $decode_dashboard_weekly->id;
        $data['week_dashboard_list'] = $decode_dashboard_weekly->data;
        $data['monthly_dashboard_id'] = $decode_dashboard_monthly->id;
        $data['monthly_dashboard_list'] = $decode_dashboard_monthly->data;

        return view('pages.backend.dashboard', compact('data'));
    }

    //this find number of week a month
    function weekOfMonth($date)
    {
        //Get the first day of the month.
        $firstOfMonth = strtotime(date("Y-m-01", $date));
        //Apply above formula.
        return intval(date("W", $date)) - intval(date("W", $firstOfMonth)) + 1;
    }

}
