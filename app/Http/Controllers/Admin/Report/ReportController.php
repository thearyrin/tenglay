<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 5/18/2018
 * Time: 11:56 AM
 */

namespace App\Http\Controllers\Admin\Report;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('auth')->except('logout');
    }

    //this function for start first page of ticket
    public function index()
    {
        return redirect('admin/report/sale');
    }

    //this function is getting sale report
    public function sale(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $data_check = $this->check_permission("SYS027", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");
        $date_start = date("d/m/Y");
        $date_end = date("d/m/Y");
        $search = 0;
        $fleet_id = -1;
        $ticket_id = -1;
        $driver_id = -1;
        $sale_id = -1;
        $fleet_name = "All";
        $ticket_number = "All";
        $driver_name = "All";
        $sale_number = "All";


        if ($request->isMethod('post')) {

            $date_start = $request->from_date;
            $date_end = $request->to_date;
            $fleet_id = $request->plate_num;
            $fleet_name = $request->fleet_name;
            $driver_id = $request->driver_id;
            $driver_name = $request->driver_name;
            $ticket_id = $request->ticket_id;
            $ticket_number = $request->ticket_number;
            $sale_id = $request->sale_id;
            $sale_number = $request->sale_number;

            $search = 1;
        }

        $from_date = Carbon::createFromFormat("d/m/Y", $date_start)->format("d-M-Y");
        $to_date = Carbon::createFromFormat("d/m/Y", $date_end)->format("d-M-Y");

        $parms = array(
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "FleetID" => $fleet_id,
            "DriverID" => $driver_id,
            "SaleID" => $sale_id,
            "TicketID" => $ticket_id,
            "UserID" => $user_id,
        );


        $call_api = $this->call_api_by_parameter("webGetTCSaleReport", $parms);
//        dd($call_api);

        $decode = json_decode($call_api);

        $data['id'] = $decode->id;
        $data['list'] = $decode->data;

        $data['date_start'] = $date_start;
        $data['date_end'] = $date_end;
        $data['search'] = $search;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;
        $data['driver_id'] = $driver_id;
        $data['driver_name'] = $driver_name;
        $data['sale_id'] = $sale_id;
        $data['sale_number'] = $sale_number;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        return view('pages.backend.reports.show', compact('data'));
    }

    //this function for export excel of sale fuel
    public function export_excel()
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $fleet_id = Session::get("fleet_id_fuel");
        $from_date = Session::get("date_start_fuel");
        $to_date = Session::get("date_end_fuel");
        $invoice = Session::get("ticket_id_fuel");
        $driver_id = Session::get("driver_id_fuel");
        $user_id = Session::get("current_user_fuel");
        $sale_id = Session::get("sale_id_fuel");

        $parms = array(
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "FleetID" => $fleet_id,
            "DriverID" => $driver_id,
            "SaleID" => $sale_id,
            "TicketID" => $invoice,
            "UserID" => $user_id,
        );

        $data_api = $this->call_api_by_parameter("webGetTCSaleReport", $parms);
        if ($data_api == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        } else {

            // Define the Excel spreadsheet headers
            $sale_list = array();
            $sale_list[] = ['Order Number', 'Start Date', 'End Date', 'Ticket Number', 'Truck Number', 'Driver', 'Trailer',
                'Mix', 'Ticket Fuel(L)', 'Pump', 'Preset(L)', 'Actual(L)'];

            $data_json = json_decode($data_api);
            $i = 1;

            if ($data_json->id) {
                foreach ($data_json->data as $row) {
                    $sale_list[] = array($row->OrderID, $row->StartDateTime, $row->EndDateTime, $row->TicketNumber,
                        $row->PlateNumber, $row->NameKh, $row->TrailerNumber, ($row->TSLStatus == 0 ? "B" : ""),
                        $row->TotalAmountFuel, $row->PumpNumber, ($row->PresetValue != "" ? $row->PresetValue : ""),
                        ($row->ActualVolume != "" ? $row->ActualVolume : ""));
                    $i++;
                }
            }

            // and append it to the payments array.
            return Excel::create('Ticket Fuel Report At ' . $from_date . '-To-' . $to_date, function ($excel) use ($sale_list, $i) {
                $excel->sheet('sheet name', function ($sheet) use ($sale_list, $i) {

                    $sheet->getStyle('A1:L1')->applyFromArray(array(
                        "font" => array(
                            "bold" => true,
                            "color" => array("rgb" => "000000"),
                        ),

                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ));

                    $sheet->fromArray($sale_list, null, 'A1', false, false);

                });
            })->download('xlsx');
        }
    }

    //this function for getting data ticket in to report
    public function get_ticket(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $request->session()->forget([
            "date_start_ticket",
            "date_end_ticket",
            "time_start_ticket",
            "time_end_ticket",
            "fleet_id_ticket",
            "driver_id_ticket",
            "reason_id_ticket",
            "destination_id_ticket",
            "invoice_ticket",
            "status_ticket",
            "login_user_ticket",
            "current_user_id",
            "team_report",
            "ref_report",
        ]);

        $data_check = $this->check_permission("SYS028", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = session("ID");
        $date_start = date("d/m/Y");
        $date_end = date("d/m/Y");
        $search = 0;
        $reason_id = '-1';
        $reason_name = "All";
        $fleet_id = '-1';
        $fleet_name = 'All';
        $ticket_id = '-1';
        $ticket_number = "All";
        $driver_id = '-1';
        $driver_name = "All";
        $destination_id = "-1";
        $des_name = "All";
        $status_id = 0;
        $login_user = $user_id;
        $time_start = '';
        $time_end = '';
        $team_leader_id = '-1';
        $teaml_leader_name = "All";
        $reference_id = "-1";
        $reference_number = "All";

        $call_user = $this->call_api_by_parameter("webGetUserInTicket", ['UserID' => $user_id]);

        if (($call_user == false)) {
            return redirect('/admin')->withErrors("You are losing your connection");
        }

        if ($request->isMethod('post')) {

            $date_start = $request->from_date;
            $date_end = $request->to_date;
            $status_id = $request->status;
            $login_user = $request->user;
            $time_start = $request->from_time;
            $time_end = $request->to_time;
            $fleet_id = $request->plate_num;
            $fleet_name = $request->fleet_name;
            $driver_id = $request->driver_id;
            $driver_name = $request->driver_name;
            $reason_id = $request->reason;
            $reason_name = $request->reason_name;
            $destination_id = $request->destination_id;
            $des_name = $request->des_name;
            $ticket_id = $request->ticket_id;
            $ticket_number = $request->ticket_number;
            $team_leader_id = $request->team_leader;
            $teaml_leader_name = $request->team_leader_name;
            $reference_id = $request->ref_no;
            $reference_number = $request->ref_no_name;
            $search = 1;
        }

        $from_date = Carbon::createFromFormat("d/m/Y", $date_start)->format("d-M-Y");
        $to_date = Carbon::createFromFormat("d/m/Y", $date_end)->format("d-M-Y");

        $parms = array(
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "Reason" => $reason_id,
            "Fleet" => $fleet_id,
            "Invoice" => $ticket_id,
            "DriverID" => $driver_id,
            "DestinationID" => $destination_id,
            "Status" => $status_id,
            "UserID" => $login_user,
            "TimeStart" => $time_start,
            "TimeEnd" => $time_end,
            "CurrentUserID" => $user_id,
            "TeamLeader" => $team_leader_id,
            "ReferenceNumber" => $reference_id,
        );


        $call_api = $this->call_api_by_parameter("webGetTicketReport", $parms);
//        dd($call_api);

        if ($call_api == false) {
            return redirect('/admin')->withErrors("You are losing your connection");
        }

        $decode_api = json_decode($call_api);
        $decode_user = json_decode($call_user);

        $data['id'] = $decode_api->id;
        $data['list'] = $decode_api->data;
        $data['user_id'] = $decode_user->id;
        $data['user_list'] = $decode_user->data;
        $data['date_start'] = $date_start;
        $data['date_end'] = $date_end;
        $data['time_start'] = $time_start;
        $data['time_end'] = $time_end;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;
        $data['reason_id'] = $reason_id;
        $data['reason_name'] = $reason_name;
        $data['driver_id'] = $driver_id;
        $data['driver_name'] = $driver_name;
        $data['des_id'] = $destination_id;
        $data['des_name'] = $des_name;
        $data['id_user'] = $login_user;
        $data['status'] = $status_id;
        $data['search'] = $search;
        $data['team_id'] = $team_leader_id;
        $data['team_name'] = $teaml_leader_name;
        $data['ref_id'] = $reference_id;
        $data['ref_number'] = $reference_number;

        session([
            "date_start_ticket" => $from_date,
            "date_end_ticket" => $to_date,
            "time_start_ticket" => $time_start,
            "time_end_ticket" => $time_end,
            "fleet_id_ticket" => $fleet_id,
            "driver_id_ticket" => $driver_id,
            "reason_id_ticket" => $reason_id,
            "destination_id_ticket" => $destination_id,
            "invoice_ticket" => $ticket_id,
            "status_ticket" => $status_id,
            "login_user_ticket" => $login_user,
            "current_user_id" => $user_id,
            "team_report" => $team_leader_id,
            "ref_report" => $reference_id,
        ]);

        return view('pages.backend.reports.data_ticket', compact('data'));
    }

    //this function for exporting data report
    public function export_ticket()
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $from_date = session("date_start_ticket");
        $to_date = session("date_end_ticket");
        $reason_id = session("reason_id_ticket");
        $fleet_id = session("fleet_id_ticket");
        $invoice = session("invoice_ticket");
        $driver_id = session("driver_id_ticket");
        $destination_id = session("destination_id_ticket");
        $status = session("status_ticket");
        $login_user = session("login_user_ticket");
        $time_start = session("time_start_ticket");
        $time_end = session("time_end_ticket");
        $current_user = session("ID");
        $team_id = session("team_report");
        $ref_id = session("ref_report");

        $parms = array(
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "Reason" => $reason_id,
            "Fleet" => $fleet_id,
            "Invoice" => $invoice,
            "DriverID" => $driver_id,
            "DestinationID" => $destination_id,
            "Status" => $status,
            "UserID" => $login_user,
            "TimeStart" => $time_start,
            "TimeEnd" => $time_end,
            "CurrentUserID" => $current_user,
            "TeamLeader" => $team_id,
            "ReferenceNumber" => $ref_id,
        );

        $data_api = $this->call_api_by_parameter("webGetTicketReportExport", $parms);

        $head_one = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'ទៅ', '',
            '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
            '', '', '', 'ទទួលបានប្រេង', '', '', '', '', '', '', '', '', '', 'មកវិញ', '', '', '', '', '', '', '', '', '', 'Diesel Return', '', '', '', '', 'Write Off', '', '', '', '', '', 'Cash Advanced', '', '', '', ''];
        $head_two = ['No', 'Ticket No', 'Issue Date', 'Issue Time', 'Expire Date', 'Expire Time',
            'Issue By', 'Truck No', 'Team', 'Driver ID', 'Driver Name', 'Trailer No', 'Team Leader', 'Reference No',
            'Purpose Code', 'Purpose', 'Destination', 'Fuel(L)', 'TicketRefNumber', 'Diesel Return No', 'DR Amount(L)', 'Add/Cut(L)',
            'Total(L)', 'PayTrip(៛)', 'Add/Cut PayTrip(៛)', 'Total PayTrip(៛)', 'MT PickUp', 'Lolo($)', 'Customer',
            'Container1', 'Feet1', 'Container2', 'Feet2', 'Note Fuel', 'Note PayTrip', 'Remark', 'Mix', 'Barcode', 'Status',
            'Order Number', 'Pump Number', 'Start Date', 'End Date', 'Preset(L)', 'Actual(L)', 'Mix Scan', 'Date', 'Time',
            'Truck No', 'Trailer No', 'RoundTrip Type', 'Customer1', 'Container1', 'Feet1', 'Customer2', 'Container2', 'Feet2',
            'Delivery Location', 'Remark', 'Diesel Return No', 'Diesel Return Amount(L)', 'Diesel Return Lolo($)', 'Diesel Return PayTrip(៛)', 'Diesel Return Remark',
            'WriteOff No', 'WriteOff Amount(L)', 'WriteOff Lolo($)', 'WriteOff PayTrip(៛)', 'WriteOff Remark', 'WriteOff Reason',
            'Cash Advanced No', 'Type', 'First Balance($)', 'Amount($)', 'Balance($)', 'First Balance(៛)', 'Amount(៛)', 'Balance(៛)'];

        if ($data_api == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        } else {

            $data_json = json_decode($data_api);
            $data = [];

            if ($data_json->id) {

                $data = json_encode($data_json->data);
                $data = json_decode($data, true);
            }

            return Excel::create('Ticket List Report At ' . $from_date . '-To-' . $to_date, function ($excel) use ($data, $head_one, $head_two) {

                $excel->sheet('Ticket Sheet', function ($sheet) use ($data, $head_one, $head_two) {

                    $sheet->fromArray($data, null, 'A2', true);
                    $sheet->row(1, $head_one);
                    $sheet->row(2, $head_two);

                    $sheet->getStyle('A1:BZ1')->applyFromArray(array(
                        "font" => array(
                            "bold" => true,
                            "color" => array("rgb" => "000000"),
                        ),

                        'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => '00ccff')
                        ),

                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ));

                    $sheet->getStyle('AN1:AT1')->applyFromArray(array(
                        'fill' => array(
                            'color' => array('rgb' => '32CD32')
                        )
                    ));

                    $sheet->getStyle('AU1:BG1')->applyFromArray(array(
                        'fill' => array(
                            'color' => array('rgb' => 'ffff00')
                        )
                    ));

                    $sheet->getStyle('BH1:BL1')->applyFromArray(array(

                        'fill' => array(
                            'color' => array('rgb' => '9ACD32')
                        ),
                    ));

                    $sheet->getStyle('BM1:BR1')->applyFromArray(array(
                        'fill' => array(
                            'color' => array('rgb' => '32CD32')
                        ),
                    ));

                    $sheet->getStyle('BS1:BZ1')->applyFromArray(array(
                        'fill' => array(
                            'color' => array('rgb' => 'ffff00')
                        )
                    ));

                    $sheet->getStyle('A2:BZ2')->applyFromArray(array(
                        "font" => array(
                            "bold" => true,
                            "color" => array("rgb" => "000000"),
                        ),
                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ));

                    $sheet->getStyle('S2:U2')->applyFromArray(array(

                        'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => '32CD32')
                        ),
                    ));

                    $sheet->getRowDimension(1)->setRowHeight(20);

                    $sheet->getStyle('A1:BZ' . $sheet->getHighestRow())
                        ->getAlignment()->setWrapText(true);

                });
            })->download('xlsx');
        }
    }

    //this function for get data diesel
    public function get_diesel(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $data_check = $this->check_permission("SYS029", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");
        $date_start = "";
        $date_end = "";
        $from_date = "";
        $to_date = "";
        $status_id = '1';
        $ticket_id = '-1';
        $ticket_number = "All";
        $fleet_id = '-1';
        $fleet_name = "All";
        $driver_id = '-1';
        $driver_name = "All";
        $credit_id = '-1';
        $credit_number = "All";
        $search = 0;

        if ($request->isMethod('post')) {

            $status_id = $request->status;
            $ticket_id = $request->ticket_id;
            $ticket_number = $request->ticket_number;
            $fleet_id = $request->fleet_id;
            $fleet_name = $request->fleet_name;
            $driver_id = $request->driver_id;
            $driver_name = $request->driver_name;
            $credit_id = $request->credit_id;
            $credit_number = $request->credit_number;
            $date_start = $request->from_date;
            $date_end = $request->from_date;
            $search = 1;

            if ($request->from_date != "") {
                $date_start = Carbon::createFromFormat("d/m/Y", $request->from_date)->format("d-M-Y");
                $from_date = $request->from_date;
            }

            if ($request->to_date != "") {
                $date_end = Carbon::createFromFormat("d/m/Y", $request->to_date)->format("d-M-Y");
                $to_date = $request->to_date;
            }
        }

        $parms = [
            "Fleet" => $fleet_id,
            "Number" => $credit_id,
            "DateStart" => $date_start,
            "DateEnd" => $date_end,
            "DriverID" => $driver_id,
            "Ticket" => $ticket_id,
            "Status" => $status_id
        ];

        $call_data = $this->call_api_by_parameter("webGetCreditReport", $parms);

        $decode_data = json_decode($call_data);

        $data['id'] = $decode_data->id;
        $data['list'] = $decode_data->data;
        $data['date_start'] = $date_start;
        $data['date_end'] = $date_end;
        $data['search'] = $search;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;
        $data['driver_id'] = $driver_id;
        $data['driver_name'] = $driver_name;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['credit_id'] = $credit_id;
        $data['credit_number'] = $credit_number;
        $data['status_id'] = $status_id;

        return view('pages.backend.reports.diesel', compact('data'));
    }

    //this function to export data excel of diesel return
    public function export_diesel()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $from_date = Session::get("date_start_return");
        $to_date = Session::get("date_end_return");
        $fleet = Session::get("fleet_id_return");
        $credit_number = Session::get("diesel_id_return");
        $status_id = Session::get("status_id_return");
        $ticket = Session::get("driver_id_return");
        $driver = Session::get("ticket_id_return");

        $parms = [
            "Fleet" => $fleet,
            "Number" => $credit_number,
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "DriverID" => $driver,
            "Ticket" => $ticket,
            "Status" => $status_id
        ];
        $call_data = $this->call_api_by_parameter("webGetDataCreditReport", $parms);

        $credit = array();
        $credit[] = ['Created', 'Diesel Return No', 'Ticket No', 'Truck No', 'Driver ID', 'Driver', 'Amount', 'Remark', 'Status'];

        if ($call_data == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");

        } else {
            $data_json = json_decode($call_data);
            $i = 1;

            if ($data_json->id) {
                foreach ($data_json->data as $row) {
                    if ($row->Status == 0) {
                        $status = "Used";
                    } elseif ($row->Status == 1) {
                        $status = "No Use";
                    } else {
                        $status = "";
                    }

                    $credit[] = array($row->CreatedDate, $row->CreditNumber, $row->TicketNumber, $row->PlateNumber, $row->CodeID, $row->NameKh, $row->Amount . " L", $row->Remark, $status);
                    $i++;
                }
            }

            // and append it to the payments array.
            return Excel::create('Diesel Return List Report At ' . $from_date . '-To-' . $to_date, function ($excel) use ($credit, $i) {
                $excel->sheet('sheet name', function ($sheet) use ($credit, $i) {

                    $sheet->getStyle('A1:I1')->applyFromArray(array(
                        "font" => array(
                            "bold" => true,
                            "color" => array("rgb" => "000000"),
                        ),

                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ));

                    $sheet->fromArray($credit, null, 'A1', false, false);

                });
            })->download('xlsx');
        }
    }

    //this function for write off report
    public function get_writeoff(Request $request)
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $data_check = $this->check_permission("SYS030", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");
        $date_start = "";
        $date_end = "";
        $from_date = "";
        $to_date = "";

        $status_id = '0';
        $fleet_id = '-1';
        $fleet_name = 'All';
        $driver_id = "-1";
        $driver_name = 'All';
        $ticket_id = "-1";
        $ticket_name = 'All';
        $writeoff_id = '-1';
        $writeoff_number = "All";
        $search = 0;

        if ($request->isMethod('post')) {
            $status_id = $request->status;
            $fleet_id = $request->fleet_id;
            $fleet_name = $request->fleet_name;
            $driver_id = $request->driver_id;
            $driver_name = $request->driver_name;
            $ticket_id = $request->ticket_id;
            $ticket_name = $request->ticket_number;
            $writeoff_id = $request->writeoff_id;
            $writeoff_number = $request->writeoff_number;

            if ($request->from_date != "") {
                $from_date = Carbon::createFromFormat("d/m/Y", $request->from_date)->format("d-M-Y");
                $date_start = $request->from_date;
            }

            if ($request->to_date != "") {
                $to_date = Carbon::createFromFormat("d/m/Y", $request->to_date)->format("d-M-Y");
                $date_end = $request->to_date;
            }

            $search = 1;
        }


        $parms = [
            "Fleet" => $fleet_id,
            "Number" => $writeoff_id,
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "DriverID" => $driver_id,
            "Ticket" => $ticket_id,
            "Status" => $status_id
        ];

        $call_data = $this->call_api_by_parameter("webGetDataWriteOffReport", $parms);

        $decode_data = json_decode($call_data);

        $data['id'] = $decode_data->id;
        $data['list'] = $decode_data->data;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['date_start'] = $date_start;
        $data['date_end'] = $date_end;
        $data['search'] = $search;
        $data['status_id'] = $status_id;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;
        $data['driver_id'] = $driver_id;
        $data['driver_name'] = $driver_name;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_name;
        $data['writeoff_id'] = $writeoff_id;
        $data['writeoff_number'] = $writeoff_number;

        return view("pages.backend.reports.writeoff", compact('data'));
    }

    //this function to export data write off
    public function export_writeoff()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $from_date = Session::get("date_start_writeoff");
        $to_date = Session::get("date_end_writeoff");
        $fleet = Session::get("fleet_id_writeoff");
        $write_number = Session::get("writeoff_id");
        $status_id = Session::get("status_id_writeoff");
        $driver = Session::get("driver_id_writeoff");
        $ticket = Session::get("ticket_id_writeoff");

        $parms = [
            "Fleet" => $fleet,
            "Number" => $write_number,
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "DriverID" => $driver,
            "Ticket" => $ticket,
            "Status" => $status_id
        ];

        $call_data = $this->call_api_by_parameter("webGetDataWriteOffReport", $parms);

        $write_off = array();
        $write_off[] = ['ID', 'Request Date', 'Request By', 'Approve Date', 'Approve By', 'Ticket No', 'Truck No',
            'Driver ID', 'Driver', 'Amount(L)', 'Reason', "Status", "Remark"];

        if ($call_data == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        } else {
            $data_json = json_decode($call_data);
            $i = 1;

            if ($data_json->id) {

                foreach ($data_json->data as $row) {
                    if ($row->Status == 0) {
                        $status = "Pending";
                    } elseif ($row->Status == 1) {
                        $status = "Approved";
                    } elseif ($row->Status == 2) {
                        $status = "Cancel";
                    }

                    $write_off[] = array($row->WriteOffNumber, $row->RequestDate, $row->RequestName, $row->ApproveDate,
                        $row->ApproveName, $row->TicketNumber, $row->PlateNumber, $row->CodeID, $row->NameKh,
                        $row->Amount . " L", $row->Reason, $status, $row->Remark);
                    $i++;
                }
            }

            // and append it to the payments array.
            return Excel::create('Write Off List Report At ' . $from_date . '-To-' . $to_date, function ($excel) use ($write_off, $i) {
                $excel->sheet('sheet name', function ($sheet) use ($write_off, $i) {

                    $sheet->getStyle('A1:M1')->applyFromArray(array(
                        "font" => array(
                            "bold" => true,
                            "color" => array("rgb" => "000000"),
                        ),

                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ));

                    $sheet->fromArray($write_off, null, 'A1', false, false);
                });
            })->download('xlsx');
        }
    }

    //this function for get report of tank delivery
    public function tank_delivery(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        Session::forget("date_start_tank");
        Session::forget("date_end_tank");

        $user_id = Session::get("ID");
        $date_start = date("d/m/Y");
        $date_end = date("d/m/Y");

        $data_check = $this->check_permission("SYS032", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $search = 0;

        if ($request->isMethod('post')) {

            $date_start = $request->from_date;
            $date_end = $request->to_date;
            $search = 1;
        }

        $from_date = Carbon::createFromFormat("d/m/Y", $date_start)->format("d-M-Y");
        $to_date = Carbon::createFromFormat("d/m/Y", $date_end)->format("d-M-Y");

        $call_tank_delivery = $this->call_api_by_parameter("webGetTankDelivery",
            [
                "StartDate" => $from_date,
                "EndDate" => $to_date
            ]);

        if ($call_tank_delivery == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $decode_json = json_decode($call_tank_delivery);

        $data['id'] = $decode_json->id;
        $data['list'] = $decode_json->data;
        $data['search'] = $search;
        $data['date_start'] = $date_start;
        $data['date_end'] = $date_end;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        Session::put("date_start_tank", $from_date);
        Session::put("date_end_tank", $to_date);

        return view("pages.backend.reports.tank_delivery", compact('data'));
    }

    //this function is for export tank
    public function export_tank()
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $start_date = Session::get("date_start_tank");
        $end_date = Session::get("date_end_tank");

        $parms = [
            "StartDate" => $start_date,
            "EndDate" => $end_date
        ];

        $call_data = $this->call_api_by_parameter("webGetTankDelivery", $parms);

        $tank_d = array();
        $tank_d[] = ['Delivery ID', 'DateTime Start', 'DateTime End', 'Tank Number', 'Total Volume(L)', 'Sale(L)', 'Start Volume(L)', 'End Volume(L)', 'Height Start', 'Height End', 'Water Start', 'Water End', 'Height Water Start', 'Height Water End', 'Source Type'];

        if ($call_data == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");

        } else {
            $data_json = json_decode($call_data);
            $i = 1;

            if ($data_json->id) {
                foreach ($data_json->data as $row) {
                    $tank_d[] = array($row->delivery_id, $row->datetime_start, $row->datetime_end, $row->TankNumber, $row->total_delivery, $row->sale_volumne, $row->StartVolume, $row->VolumeEnd, $row->VolumeHeighStart, $row->VolumeHeighEnd, $row->water_start, $row->water_end, $row->water_height_start, $row->water_height_end, $row->source_type);
                    $i++;
                }
            }

            // and append it to the payments array.
            return Excel::create('Tank Delivery List Report At ' . $end_date . '-To-' . $start_date, function ($excel) use ($tank_d, $i) {
                $excel->sheet('sheet name', function ($sheet) use ($tank_d, $i) {

                    $sheet->getStyle('A1:N1')->applyFromArray(array(
                        "font" => array(
                            "bold" => true,
                            "color" => array("rgb" => "000000"),
                        ),

                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ));

                    $sheet->fromArray($tank_d, null, 'A1', false, false);

                });
            })->download('xlsx');
        }
    }

    //this function is for reconciliation
    public function reconciliation(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $user_id = Session::get("ID");
        $date_start = date("d/m/Y");
        $date_end = date("d/m/Y");

        $data_check = $this->check_permission("SYS031", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $call_station = $this->call_api_by_parameter("webGetStation", ["UserID" => $user_id]);
        $decode_station = json_decode($call_station);
        $station_id = "999999";

        if ($decode_station->id) {
            $station_id = $decode_station->data[0]->StationNumber;
        }

        $call_tank = $this->call_api_by_parameter("webGetTank", ["UserID" => $user_id, "StationNumber" => $station_id]);
        $decode_tank = json_decode($call_tank);
        $tank_id = 1;
        if ($decode_tank->id) {
            $tank_id = $decode_tank->data[0]->TankNumber;
        }

        $search = 0;
        $time_start = '00:00:00';
        $time_end = '12:00:00';

        if ($request->isMethod('post')) {

            $date_start = $request->from_date;
            $date_end = $request->to_date;
            $time_start = $request->from_time;
            $time_end = $request->to_time;
            $search = 1;
            $tank_id = $request->tank_number;
        }


        $date_time_start = Carbon::createFromFormat("d/m/Y H:i:s", $date_start . $time_start)->format("d-M-Y H:i:s");
        $date_time_end = Carbon::createFromFormat("d/m/Y H:i:s", $date_end . $time_end)->format("d-M-Y H:i:s");

        $parms_used = [
            "StartDateTime" => $date_time_start,
            "EndDateTime" => $date_time_end,
            "TankNumber" => $tank_id,
            "StationNumber" => $station_id
        ];

        $call_con = $this->call_api_by_parameter("webGetReconciliationReport", $parms_used);

        $total_used = 0;
        $total_delivery = 0;
        $start_vol = 0;
        $end_vol = 0;
        $current_vol = 0;

        if ($call_con) {

            $decode_con = json_decode($call_con);

            if ($decode_con->id) {
                $total_used = $decode_con->data[0]->TotalUsed;
                $total_delivery = $decode_con->data[0]->Dedivery;
                $start_vol = $decode_con->data[0]->StartStock;
                $end_vol = $decode_con->data[0]->EndStock;
                $current_vol = $decode_con->data[0]->CurrentStock;
            }
        }
        $data['id'] = 0;
        $data['list'] = '';
        $data['search'] = $search;
        $data['date_start'] = $date_start;
        $data['date_end'] = $date_end;
        $data['time_start'] = $time_start;
        $data['time_end'] = $time_end;
        $data['total_used'] = $total_used;
        $data['total_delivery'] = $total_delivery;
        $data['start_vol'] = $start_vol;
        $data['end_vol'] = $end_vol;
        $data['current_vol'] = $current_vol;
        $data['tank_id'] = $decode_tank->id;
        $data['tank_list'] = $decode_tank->data;
        $data['id_tank'] = $tank_id;

        return view("pages.backend.reports.reconciliation", compact('data'));
    }

    //this function to get fleet in sale transaction
    public function get_fleet_in_sale(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);
        $data_json = $this->call_api_by_parameter("webGetFleetInSale", ['PlateNumber' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get driver in sale transaction
    public function get_driver_in_sale(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetDriverInSale", ['DriverName' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get driver in ticket
    public function get_number_sale(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetNumberSale", ['Number' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get driver in sale transaction
    public function get_ticket_in_sale(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetTicketInSale", ['Number' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get fleet in ticket
    public function get_fleet_in_ticket(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);
        $data_json = $this->call_api_by_parameter("webGetFleetInTicket", ['PlateNumber' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get driver in ticket
    public function get_driver_in_ticket(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetDriverInTicket", ['DriverName' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get reason in ticket
    public function get_reason_in_ticket(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetReasonInTicket", ['Reason' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get destination in ticket
    public function get_destination_in_ticket(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetDestinationInTicket", ['Code' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get ticket number
    public function get_ticket_number(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetTicketNumber", ['Number' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get driver in ticket
    public function get_fleet_in_credit(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetFleetInCredit", ['PlateNumber' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get reason in ticket
    public function get_driver_in_credit(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetDriverInCredit", ['DriverName' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get destination in ticket
    public function get_ticket_in_credit(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetTicketInCredit", ['Number' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get ticket number
    public function get_credit_number(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetCreditNumber", ['Number' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get driver in ticket
    public function get_fleet_in_writeoff(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetFleetInWriteOff", ['PlateNumber' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get reason in ticket
    public function get_driver_in_writeoff(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetDriverInWriteOff", ['DriverName' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get destination in ticket
    public function get_ticket_in_writeoff(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetTicketInWriteOff", ['Number' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get ticket number
    public function get_writeoff_number(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetWriteOffNumber", ['Number' => $term, "UserID" => $user_id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get get_reference_number data by filter
    public function get_reference_number(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetReferenceNumberFilterGroup", ['Name' => $term, "UserID" => $user_id]);
        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    public function get_team_leader(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetTeamLeaderInTicket", ['Name' => $term, "UserID" => $user_id]);
        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function for get report of tank delivery
    public function account(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        Session::forget("date_start_account");
        Session::forget("date_end_account");

        $user_id = Session::get("ID");
        $date_start = date("d/m/Y");
        $date_end = date("d/m/Y");
        $truck_id = -1;
        $truck_number = 'All';
        $ticket_id = -1;
        $ticket_number = "All";
        $login_user = -1;

        $data_check = $this->check_permission("SYS042", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $search = 0;

        $call_user = $this->call_api_by_parameter("webGetUserInTopUp", ['UserID' => $user_id]);

        if (($call_user == false)) {
            return redirect('/admin')->withErrors("You are losing your connection");
        }

        if ($request->isMethod('post')) {

            $date_start = $request->from_date;
            $date_end = $request->to_date;
            $truck_id = $request->fleet_id;
            $truck_number = $request->fleet_name;
            $ticket_id = $request->ticket_id;
            $ticket_number = $request->ticket_number;
            $login_user = $request->user_id;
            $search = 1;
        }

        $from_date = Carbon::createFromFormat("d/m/Y", $date_start)->format("d-M-Y");
        $to_date = Carbon::createFromFormat("d/m/Y", $date_end)->format("d-M-Y");

        $call_balance_his = $this->call_api_by_parameter("webGetAccountBalanceHistory",
            [
                "StartDate" => $from_date,
                "EndDate" => $to_date,
                "TruckID" => $truck_id,
                "TicketID" => $ticket_id,
                "UserID" => $login_user
            ]);

        if ($call_balance_his == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $decode_json = json_decode($call_balance_his);
        $decode_user = json_decode($call_user);
//        dd($decode_json);

        $data['id'] = $decode_json->id;
        $data['list'] = $decode_json->data;
        $data['search'] = $search;
        $data['date_start'] = $date_start;
        $data['date_end'] = $date_end;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['truck_id'] = $truck_id;
        $data['truck_number'] = $truck_number;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['login_user'] = $login_user;
        $data['user_id'] = $decode_user->id;
        $data['user_list'] = $decode_user->data;

        Session::put("date_start_account", $from_date);
        Session::put("date_end_account", $to_date);

        return view("pages.backend.reports.account_balance", compact('data'));
    }

    //this function to get get_reference_number data by filter
    public function get_ticket_intopup(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetTicketInTopup", ['Number' => $term, "UserID" => $user_id]);
        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    public function get_fleet_intopup(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetFleetInTopup", ['PlateNumber' => $term, "UserID" => $user_id]);
        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

}