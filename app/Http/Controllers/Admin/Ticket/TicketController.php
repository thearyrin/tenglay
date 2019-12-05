<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/25/2018
 * Time: 11:29 AM
 */

namespace App\Http\Controllers\Admin\Ticket;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use \Milon\Barcode\DNS1D;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class TicketController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validation_create_ticket = [
        'issue_date' => 'required',
        'expired_date' => 'required',
        'truck_number' => 'required',
        'trailer' => 'required',
        'driver' => 'required'
    ];

    protected $rule_barcode = [
        'barcode' => "required"
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('logout');
    }

    //this function for start first page of ticket
    public function index()
    {
        return redirect('admin/ticket/list');
    }

    //this function for show ticket list
    public function show(Request $request)
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $request->session()->forget([
            'date_start_list_ticket',
            'date_end_list_ticket',
            'time_start_list_ticket',
            'time_end_list_ticket',
            'date_fleet_list_ticket',
            'data_fleet_name_ticket',
            'driver_id_list_ticket',
            'driver_name_list_ticket',
            'date_reason_list_ticket',
            'date_reason_name_ticket',
            'destination_id_list_ticket',
            'destination_name_list_ticket',
            'invoice_list_ticket',
            'invoice_number_ticket',
            'search_list_ticket',
            'status_list_ticket',
            'login_user_list_ticket',
            'team_leader_id_ticket',
            'team_leader_name_ticket',
            'reference_id_ticket',
            'reference_number_ticket',
            'user_id_logged',
        ]);

        $data_check = $this->check_permission("SYS022", "view");

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

        $data['add'] = CheckPemission($this->check_permission("SYS022", 'add'));
        $data['edit'] = CheckPemission($this->check_permission("SYS022", 'edit'));
        $data['extend'] = CheckPemission($this->check_permission("SYS036", 'add'));
        $data['add_credit'] = $this->check_permission("SYS023", 'add');
        $data['add_writeoff'] = $this->check_permission("SYS024", 'add');

        session([
            "date_start_list_ticket" => $from_date,
            "date_end_list_ticket" => $to_date,
            "time_start_list_ticket" => $time_start,
            "time_end_list_ticket" => $time_end,
            "date_fleet_list_ticket" => $fleet_id,
            "data_fleet_name_ticket" => $fleet_name,
            "driver_id_list_ticket" => $driver_id,
            "driver_name_list_ticket" => $driver_name,
            "date_reason_list_ticket" => $reason_id,
            "date_reason_name_ticket" => $reason_name,
            "destination_id_list_ticket" => $destination_id,
            "destination_name_list_ticket" => $des_name,
            "invoice_list_ticket" => $ticket_id,
            "invoice_number_ticket" => $ticket_number,
            "search_list_ticket" => $search,
            "status_list_ticket" => $status_id,
            "login_user_list_ticket" => $login_user,
            "user_id_logged" => $user_id,
            "team_leader_id_ticket" => $team_leader_id,
            "team_leader_name_ticket" => $teaml_leader_name,
            "reference_id_ticket" => $reference_id,
            "reference_number_ticket" => $reference_number,
        ]);

        return view("pages.backend.ticket.show", compact('data'));
    }

    //this function to store data ticket
    public function data()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $from_date = session('date_start_list_ticket');
        $to_date = session('date_end_list_ticket');
        $user_id = session('user_id_logged');
        $reason_id = session('date_reason_list_ticket');
        $fleet_id = session('date_fleet_list_ticket');
        $ticket_id = session('invoice_list_ticket');
        $driver_id = session('driver_id_list_ticket');
        $destination_id = session('destination_id_list_ticket');
        $status_id = session('status_list_ticket');
        $login_user = session('login_user_list_ticket');
        $time_start = session('time_start_list_ticket');
        $time_end = session('time_end_list_ticket');
        $ticket_number = session('invoice_number_ticket');
        $fleet_name = session('data_fleet_name_ticket');
        $reason_name = session('date_reason_name_ticket');
        $driver_name = session('driver_name_list_ticket');
        $des_name = session('destination_name_list_ticket');
        $search = session('search_list_ticket');
        $team_id = session('team_leader_id_ticket');
        $team_name = session('team_leader_name_ticket');
        $ref_id = session('reference_id_ticket');
        $ref_number = session('reference_number_ticket');

        $date_start = date("d/m/Y", strtotime($from_date));
        $date_end = date("d/m/Y", strtotime($to_date));

        $data_check = $this->check_permission("SYS022", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $call_user = $this->call_api_by_parameter("webGetUserInTicket", ['UserID' => $user_id]);

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
            "TeamLeader" => $team_id,
            "ReferenceNumber" => $ref_id,
        );

        $call_api = $this->call_api_by_parameter("webGetTicketReport", $parms);


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
        $data['team_id'] = $team_id;
        $data['team_name'] = $team_name;
        $data['ref_id'] = $ref_id;
        $data['ref_number'] = $ref_number;

        $data['add'] = CheckPemission($this->check_permission("SYS022", 'add'));
        $data['edit'] = CheckPemission($this->check_permission("SYS022", 'edit'));
        $data['extend'] = CheckPemission($this->check_permission("SYS036", 'add'));
        $data['add_credit'] = $this->check_permission("SYS023", 'add');
        $data['add_writeoff'] = $this->check_permission("SYS024", 'add');

        return view("pages.backend.ticket.show", compact('data'));
    }

    //this function is for exporting data
    public function export(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $from_date = session('date_start_list_ticket');
        $to_date = session('date_end_list_ticket');
        $reason_id = session('date_reason_list_ticket');
        $fleet_id = session('date_fleet_list_ticket');
        $invoice = session('invoice_list_ticket');
        $driver_id = session('driver_id_list_ticket');
        $destination_id = session('destination_id_list_ticket');
        $status = session('status_list_ticket');
        $login_user = session('login_user_list_ticket');
        $time_start = session('time_start_list_ticket');
        $time_end = session('time_end_list_ticket');
        $current_user = session('user_id_logged');
        $team_id = session('team_leader_id_ticket');
        $ref_id = session('reference_id_ticket');

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

    //this function is for extending expired ticket
    public function extend(Request $request)
    {
        $ticket_id = $request->id;
        $ticket_number = $request->number;
        $user_id = session("ID");

        $parm = [
            "TicketID" => $ticket_id,
            "UserID" => $user_id
        ];

        $call_api_request = $this->call_api_by_parameter("webAddExtendExpiredTicket", $parm);

        if ($call_api_request) {

            $decode_request = json_decode($call_api_request);

            if ($decode_request->id) {

                $ticket_id = $decode_request->data[0]->TicketID;

                $get_ticket_de = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $ticket_id]);

                if ($get_ticket_de) {

                    $encode_detail = json_decode($get_ticket_de);

                    if ($encode_detail->id) {

                        $data['data'] = $encode_detail->data;
                        $data['barcode'] = $encode_detail->data[0]->Barcode;
                        $data['img'] = DNS1D::getBarcodePNG($encode_detail->data[0]->Barcode, 'C39');

                        $label = view('pages.backend.ticket.print_new', compact('data'))->render();

                        return response()->json([
                            'error' => 0,
                            'Label' => $label
                        ]);
                    }

                    return response()->json([
                        'error' => "something wrong with your data."
                    ]);
                }

                return response()->json([
                    'error' => 'You are losing your connection. Please check your connection'
                ]);
            }

            return response()->json([
                'error' => "No data."
            ]);
        }
    }

    //this function is for reversing expired ticket
    public function reverse(Request $request)
    {
        $ticket_id = $request->id;
        $ticket_number = $request->number;

        $user_id = session("ID");

        $parm = [
            "TicketID" => $ticket_id,
            "UserID" => $user_id
        ];

        $call_api_request = $this->call_api_by_parameter("webReverseTicket", $parm);

        if ($call_api_request) {

            $decode_request = json_decode($call_api_request);

            if ($decode_request->id) {

                return response()->json([
                    'error' => 0,
                    'msg' => "Reverse success"
                ]);
            }

            return response()->json([
                'error' => "No data."
            ]);
        }
    }

    //this function is for detail of ticket
    public function detail($id)
    {

        $ticet_detail = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $id]);

        $user_id = session("ID");

        if ($ticet_detail == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $call_round = $this->call_api_by_parameter("webGetRoundTripByTicketNumber", ["TicketID" => $id]);
        $call_write_off = $this->call_api_by_parameter("webGetWriteOffByTicketNumber", ["TicketID" => $id]);
        $call_return = $this->call_api_by_parameter("webGetCreditNoteByID", ["TicketID" => $id]);
        $use_return = $this->call_api_by_parameter("webGetCreditUsedByTicketNumber", ["TicketID" => $id]);
        $ticket_his = $this->call_api_by_parameter("webGetTicketHistoryByTicketNumber", ["TicketID" => $id]);
        $request = $this->call_api_by_parameter("webGetRequestByTicketNumber", ["TicketID" => $id]);
        $extend = $this->call_api_by_parameter("webGetExtendByTicketNumber", ["TicketID" => $id]);
        $rescan = $this->call_api_by_parameter("webGetRescanByTicketNumber", ["TicketID" => $id]);

        $decode = json_decode($ticet_detail);
        $decode_round = json_decode($call_round);
        $decode_writeoff = json_decode($call_write_off);
        $decode_return = json_decode($call_return);
        $decode_used = json_decode($use_return);
        $decode_history = json_decode($ticket_his);
        $decode_request = json_decode($request);
        $decode_extend = json_decode($extend);
        $decode_rescan = json_decode($rescan);

        if ($decode->id == 0) {
            return redirect('/admin/ticket');
        }
        $data['id'] = $id;
        $data['list'] = $decode->data;
        $data['row'] = $decode->data[0];
        $data['id_list'] = $decode->id;

        $data['id_round'] = $decode_round->id;
        $data['list_round'] = $decode_round->data;
        $data['id_write'] = $decode_writeoff->id;
        $data['list_write'] = $decode_writeoff->data;
        $data['id_return'] = $decode_return->id;
        $data['list_return'] = $decode_return->data;
        $data['id_used'] = $decode_used->id;
        $data['list_used'] = $decode_used->data;
        $data['history_id'] = $decode_history->id;
        $data['history_list'] = $decode_history->data;
        $data['request_id'] = $decode_request->id;
        $data['request_list'] = $decode_request->data;
        $data['extend_id'] = $decode_extend->id;
        $data['extend_list'] = $decode_extend->data;
        $data['rescan_id'] = $decode_rescan->id;
        $data['rescan_list'] = $decode_rescan->data;

        $data['edit'] = CheckPemission($this->check_permission("SYS022", 'edit'));
        $data['add'] = CheckPemission($this->check_permission("SYS022", 'add'));

        return view('pages.backend.ticket.detail', compact('data'));

    }

    //this function for reprinting ticket
    public function RePrint(Request $request)
    {

        $get_ticket_de = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $request->ticket_id]);

        if ($get_ticket_de) {

            $encode_detail = json_decode($get_ticket_de);

            if ($encode_detail->id) {

                $data['data'] = $encode_detail->data;
                $data['barcode'] = $encode_detail->data[0]->Barcode;
                $data['img'] = DNS1D::getBarcodePNG($encode_detail->data[0]->Barcode, 'C39');

                $label = view('pages.backend.ticket.print_new', compact('data'))->render();

                return response()->json([
                    'error' => 0,
                    'Label' => $label
                ]);
            }

            return response()->json([
                'error' => "something wrong with your data."
            ]);
        }

        return response()->json([
            'error' => 'You are losing your connection. Please check your connection'
        ]);
    }

    //this function to update status of ticket
    public function update_status(Request $request)
    {
        $parms = [
            "TicketID" => $request->ticket_id,
            'Status' => $request->status,
            'Remark' => $request->remark,
            'UserID' => session("ID")
        ];

        $ticet_detail = $this->call_api_by_parameter("webEditTicketStatus", $parms);

        if ($ticet_detail) {

            $encode_api = json_decode($ticet_detail);
            $message = $encode_api->message;

            if ($encode_api->id) {
                return response()->json([
                    'error' => 0,
                    'message' => 'Your data was updated.'
                ]);
            }

            return response()->json([
                'error' => $message
            ]);
        }
        return response()->json([
            'error' => 'You are losing your connection. Please check your connection'
        ]);
    }

    //this function to update status of ticket
    public function update_remark(Request $request)
    {

        $ticet_detail = $this->call_api_by_parameter("webEditTicketRemark",
            [
                "TicketID" => $request->ticket_id,
                'Remark' => $request->remark
            ]);

        if ($ticet_detail == false) {
            return response()->json([
                'msg' => "You are losing your connection."
            ]);
        }
        $json = json_decode($ticet_detail);
        return response()->json([
            'msg' => $json->id
        ]);
    }

    //this function for updating expired date
    public function update_date(Request $request)
    {
        $date_expired = Carbon::createFromFormat("d-m-Y h:i:s A", $request->date_time)->format("d-M-Y h:i:s A");
        $call_api = $this->call_api_by_parameter("webUpdateExpiredDateTicket", ["ExpiredDate" => $date_expired, "TicketID" => $request->ticket_id]);
        if ($call_api) {
            $json = json_decode($call_api);
            return response()->json([
                'id' => $json->id
            ]);
        }
        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function for get credit now data
    public function credit(Request $request)
    {
        $credit_note = $this->call_api_by_parameter("webCheckCreditNote", ["DriverID" => $request->driver_id, "FleetID" => $request->fleet_id]);

        $credit_list = json_decode($credit_note);

        return response()->json([
            'credit_id' => $credit_list->id,
            'data' => $credit_list->data
        ]);

    }

    //this function to update status
    public function used_update(Request $request)
    {
        $trailer_id = $request->trailer_number;
        $purpose_id = $request->purpose_name;
        $driver_id = $request->driver_name;
        $ticket_id = $request->ticket_id;

        $parms = [
            "TicketID" => $ticket_id,
            "TrailerID" => $trailer_id,
            "DriverID" => $driver_id,
            "PurposeID" => $purpose_id
        ];

        $call_ticket = $this->call_api_by_parameter("webUpdateTicket", $parms);

        $count = count($request->ticket_detail_id);

        for ($i = 0; $i < $count; $i++) {

            $detail_id = $request->ticket_detail_id[$i];
            $container_id = $request->container_number[$i];
            $customer_id = $request->customer_name[$i];

            $parms_deatil = [
                "DetailID" => $detail_id,
                "TicketID" => $ticket_id,
                "ContainerID" => $container_id,
                "CustomerID" => $customer_id
            ];

            $call_ticket_detail = $this->call_api_by_parameter("webUpdateTicketDetail", $parms_deatil);
        }

        return redirect('/admin/ticket/detail/' . $ticket_id);
    }

    //this function to get data credit note
    public function get_credit(Request $request)
    {

        $user_id = session("ID");
        $data_json = $this->call_api_by_parameter("webGetTicketDetail", ['TicketID' => $request->id]);
        if (($data_json == false)) {
            return response()->json([
                'result' => "You are losing your connection."
            ]);
        }

        $decode = json_decode($data_json);
        if ($decode->id) {

            $data['ticket_id'] = $request->id;
            $data['ticket_number'] = $request->number;
            $data['total_fuel'] = $request->total_fuel;
            $data['total_paytrip'] = $request->total_paytrip;
            $data['id_ticket'] = $decode->id;
            $data['list'] = $decode->data;
            $data['row'] = $decode->data[0];
            $data['user_id'] = $user_id;
            $data['check_credit'] = CheckPemission($this->check_permission("SYS023", 'add'));

            $result = view("pages.backend.ticket.credit", compact('data'))->render();

            return response()->json([
                'result' => $result
            ]);
        }

        return response()->json([
            'error' => "No data."
        ]);
    }

    //this function to get data write off
    public function get_writeoff(Request $request)
    {

        $user_id = session("ID");
        $data_json = $this->call_api_by_parameter("webGetTicketDetail", ['TicketID' => $request->id]);

        if (($data_json == false)) {
            return response()->json([
                'result' => "You are losing your connection."
            ]);
        }

        $decode = json_decode($data_json);

        if ($decode->id) {
            $data['ticket_id'] = $request->id;
            $data['ticket_number'] = $request->number;
            $data['id_ticket'] = $decode->id;
            $data['list'] = $decode->data;
            $data['row'] = $decode->data[0];
            $data['total_fuel'] = $request->total_fuel;
            $data['total_paytrip'] = $request->total_paytrip;
            $data['user_id'] = $user_id;
            $data['check_writeoff'] = CheckPemission($this->check_permission("SYS024", "add"));

            $result = view("pages.backend.ticket.writeoff", compact('data'))->render();

            return response()->json([
                'result' => $result,
            ]);
        }

        return response()->json([
            'error' => "No data."
        ]);
    }

    //this function to get data round
    public function get_round_trip(Request $request)
    {
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetRoundTripByTicketNumber", ['TicketID' => $request->id]);

        if (($data_json == false)) {
            return response()->json([
                'result' => "You are losing your connection."
            ]);
        }

        $decode = json_decode($data_json);

        if ($decode->id) {


            $data['ticket_id'] = $request->id;
            $data['ticket_number'] = $request->number;
            $data['id_data'] = $decode->id;
            $data['list_data'] = $decode->data;
            $data['user_id'] = $user_id;

            $result = view("pages.backend.ticket.round", compact('data'))->render();

            return response()->json([
                'result' => $result,
            ]);
        }

        return response()->json([
            'result' => "No data."
        ]);
    }

    //this function get advanced pay
    public function get_advance_pay(Request $request)
    {
        $data_json = $this->call_api_by_parameter("webGetAdvancedPayViaID", ['ReasonID' => $request->reason_id]);

        if (($data_json == false)) {
            return response()->json([
                'error' => true,
                "msg" => "You are losing your connection."
            ]);
        }

        $decode = json_decode($data_json);

        if ($decode->id) {

            $id = $decode->id;
            $list = $decode->data;

            return response()->json([
                'id' => $id,
                'list' => $list,
                'error' => false
            ]);
        }

        return response()->json([
            'error' => true,
            "msg" => "No MT-Pickup"

        ]);
    }

    //this function to get pay trip
    public function get_pay_trip(Request $request)
    {
        $data_json = $this->call_api_by_parameter("webGetPayTripViaID", ['ReasonID' => $request->reason_id, "DestinationID" => $request->destination_id]);

        if (($data_json == false)) {
            return response()->json([
                'error' => true,
                "msg" => "You are losing your connection."
            ]);
        }

        $decode = json_decode($data_json);

        if ($decode->id) {

            $id = $decode->data[0]->ID;
            $pay_trip = $decode->data[0]->PayTrip;

            return response()->json([
                'id' => $id,
                'pay_trip' => $pay_trip,
                'error' => false
            ]);
        }

        return response()->json([
            'error' => true,
            "msg" => "No pay trip"
        ]);
    }

    //this function for create ticket form
    public function create()
    {
        $data_check = $this->check_permission("SYS022", 'add');

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = session("ID");
        $call_api_reason = $this->call_api_by_parameter("webGetReason", array("UserID" => $user_id));
        $call_api_destination = $this->call_api_by_parameter("webGetDestination", array("UserID" => $user_id));

        $encode_reason = json_decode($call_api_reason);
        $encode_destination = json_decode($call_api_destination);

        if (($call_api_reason) || ($call_api_destination)) {
            $data['user_id'] = $user_id;
            $data['id_reason'] = $encode_reason->id;
            $data['list_reason'] = $encode_reason->data;
            $data['id_destination'] = $encode_destination->id;
            $data['list_destination'] = $encode_destination->data;
            return view("pages.backend.ticket.create", compact('data'));
        }
        return redirect('admin')->with("Your are losing your connection.");
    }

    //This function is for get route information
    public function CloneData(Request $request)
    {
        $user_id = session("ID");
        $data['id'] = $request->id;

        $result = view("pages.backend.ticket.data-add", compact('data'))->render();

        return response()->json([
            'result' => $result
        ]);
    }

    //this function is for getting data Feet by container number
    public function GetFeet(Request $request)
    {

        $k = 0;

        $date = date("Y-m-d H:i:s A");
        $destination_id = "";
        $call_api_con = $this->call_api_by_parameter("webGetFeetByContainer", ["ContainerID" => $request->container_id]);
        if (count($request->destination_id) > 0) {
            foreach ($request->destination_id as $data_destination) {
                $k++;
                $coma = ",";
                if (count($request->destination_id) == $k) {
                    $coma = "";
                }

                $destination_id .= $data_destination . $coma;
            }
        }
        $con_exist = $this->call_api_by_parameter("webGetContainerExist",
            [
                "ContainerID" => $request->container_id,
                "DestinationID" => $destination_id,
                'DateTime' => $date
            ]
        );

        if ($call_api_con) {

            $deconde_json = json_decode($call_api_con);
            $con_decode = json_decode($con_exist);

            if ($deconde_json->id) {

                $ticket_id = '';
                $ticket_number = '';

                if ($con_decode->id) {
                    $ticket_id = $con_decode->data[0]->ID;
                    $ticket_number = $con_decode->data[0]->TicketNumber;
                }

                return response()->json([
                    'id' => $deconde_json->id,
                    'data' => $deconde_json->data[0],
                    'id_con' => $con_decode->id,
                    'ticket_id' => $ticket_id,
                    'ticket_number' => $ticket_number
                ]);
            }
            return response()->json([
                'id' => 0
            ]);
        }
        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function is for getting data Feet by container number
    public function get_feet_by_number(Request $request)
    {
        $date = date("Y-m-d H:i:s A");
        $call_api_con = $this->call_api_by_parameter("webGetFeetByContainerNumber", ["ContainerNumber" => $request->container_number]);

        if ($call_api_con) {

            $deconde_json = json_decode($call_api_con);

            if ($deconde_json->id) {

                return response()->json([
                    'id' => $deconde_json->id,
                    'data' => $deconde_json->data[0]
                ]);
            }
            return response()->json([
                'id' => 0
            ]);
        }
        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get driver list
    public function get_driver($plate_number)
    {
        $date = date("Y-m-d H:i:s A");
        $call_api_driver = $this->call_api_by_parameter("webGetFleetDriver", ["FleetID" => $plate_number]);
        $call_team = $this->call_api_by_parameter("webGetFleetTeam", ["FleetID" => $plate_number]);
        $call_fleet_exist = $this->call_api_by_parameter("webGetFleetExist", ["FleetID" => $plate_number, "DateTime" => $date]);

        if ($call_api_driver) {

            $deconde_json = json_decode($call_api_driver);
            $decode_fleet = json_decode($call_fleet_exist);
            $decode_team = json_decode($call_team);
            $ticket_id = '';
            $ticket_number = '';
            $team_id = 0;
            $fuel_add = 0;
            $team_name = '';

            if ($decode_fleet->id) {
                $ticket_id = $decode_fleet->data[0]->ID;
                $ticket_number = $decode_fleet->data[0]->TicketNo;
            }

            if ($decode_team->id) {
                $team_id = $decode_team->id;
                $fuel_add = $decode_team->data[0]->FuelAdd;
                $team_name = $decode_team->data[0]->Team;
            }

            return response()->json([
                'id' => $deconde_json->id,
                'data' => $deconde_json->data,
                'id_fleet' => $decode_fleet->id,
                'ticket_id' => $ticket_id,
                'ticket_number' => $ticket_number,
                'id_team' => $team_id,
                'list_team' => $team_name,
                'fuel_add' => $fuel_add,
            ]);
        }
        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get driver exist
    public function exist_driver($driver_id)
    {
        $date = date("Y-m-d H:i:s A");
        $driver_exist = $this->call_api_by_parameter("webGetDriverExist", ["DriverID" => $driver_id, "DateTime" => $date]);
        if ($driver_exist) {

            $driver_list = json_decode($driver_exist);
            $ticket_id = '';
            $ticket_number = '';

            if ($driver_list->id) {

                $ticket_id = $driver_list->data[0]->ID;
                $ticket_number = $driver_list->data[0]->TicketNo;
            }
            return response()->json([
                'id_driver' => $driver_list->id,
                'ticket_id' => $ticket_id,
                'ticket_number' => $ticket_number
            ]);
        }
        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get trailer exist
    public function exist_trailer($driver_id)
    {
        $date = date("Y-m-d H:i:s A");
        $trailer_exist = $this->call_api_by_parameter("webGetTrailerExist", ["TrailerID" => $driver_id, "DateTime" => $date]);

        if ($trailer_exist) {

            $trailer_list = json_decode($trailer_exist);
            $ticket_id = '';
            $ticket_number = '';

            if ($trailer_list->id) {

                $ticket_id = $trailer_list->data[0]->ID;
                $ticket_number = $trailer_list->data[0]->TicketNumber;
            }

            return response()->json([
                'trailer_id' => $trailer_list->id,
                'ticket_id' => $ticket_id,
                'ticket_number' => $ticket_number
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function is for saving data ticket
    public function save(Request $request)
    {

        $user_id = session("ID");
        $group_id = session("group_id");
        $group_name = session("group_name");
        $status_default = session("StatusDefault");

        $validation = Validator::make($request->all(), $this->validation_create_ticket);

        if ($validation->fails()) {
            return response()->json([
                'error' => $validation->errors()->first()
            ]);
        }

        $date_issue = Carbon::createFromFormat("d/m/Y h:i:s A", $request->issue_date)->format("d-M-Y h:i:s A");
        $date_expired = Carbon::createFromFormat("d/m/Y h:i:s A", $request->expired_date)->format("d-M-Y h:i:s A");

        $status = 0;
        $IsOnlyMoney = 0;

        $count_credit = count($request->credit_note);
        $diesel_return_amount = 0;

        //check credit is available
        if ($count_credit > 0) {
            $get_credit = $this->call_api_by_parameter("webGetCreditNoteFilterByID", ["UserID" => $user_id, "CreditID" => $request->credit_note]);
            if ($get_credit) {
                $decode_credit = json_decode($get_credit);
                if ($decode_credit->id) {

                    $credit_id = "";
                    $count_credit_data = count($decode_credit->data);
                    $k = 0;

                    foreach ($decode_credit->data as $data_credit) {
                        $k++;
                        $coma = ",";
                        if ($count_credit_data == $k) {
                            $coma = "";
                        }

                        $credit_id .= $data_credit->ID . $coma;
                        $diesel_return_amount += $data_credit->Amount;
                    }
                }
            }
        }

        //start ticket
        $data = array(
            "IssueDateTime" => $date_issue,
            "ExpireDateTime" => $date_expired,
            "UserID" => $user_id,
            "StationID" => "",
            "TrailerID" => $request->trailer,
            "FleetID" => $request->truck_number,
            "DriverID" => $request->driver,
            "TotalAmountFuel" => ($diesel_return_amount > 0 ? (($request->total_all_amount_original) + ((-1) * $diesel_return_amount)) : $request->total_all_amount_original),
            "TotalPayTripAmount" => $request->total_amount_pay_trip,
            "LoloAmount" => $request->total_amount_mtpickup,
            "Status" => $status
        );

        $call_ticket = $this->call_api_by_parameter("webAddTicket", $data);

        if ($call_ticket) {

            $encode_ticket = json_decode($call_ticket);

            if ($encode_ticket->id) {

                $ticket_id = $encode_ticket->data[0]->ID;

                if ($status_default == 0) {

                    $ticket_number = $encode_ticket->data[0]->TicketNumber;
                } else {
                    $ticket_number = $group_name . $encode_ticket->data[0]->TicketNumber;
                }

                $count_destination = count($request->destination);

                $container1_id = 0;
                $container2_id = 0;
                $customer_id = 0;
                $amount_paytrip = 0;
                $amount_mt = 0;
                $amount_fuel = 0;
                $count_con1 = 0;
                $count_con2 = 0;

                for ($i = 0; $i < $count_destination; $i++) {

                    if ($request->destination[$i] != "") {

                        if ($i === 0) {
                            $status_detail = 0;
                        } else {
                            $status_detail = 1;
                        }

                        if ($request->lolo[$i] != "") {
                            $amount_mt += $request->lolo[$i];
                        }

                        if (($request->container1[$i] != "") && ($request->container1[$i] != "28257")) {
                            $count_con1++;
                        }

                        if (($request->container2[$i] != "") && ($request->container2[$i] != "28257")) {
                            $count_con2++;
                        }

                        $amount_paytrip += $request->total_amount_paytrip[$i];
                        $amount_fuel += $request->total_amount[$i];

                        //get customer id
                        $get_customer_id = $this->call_api_by_parameter("webGetCustomerID",
                            [
                                "CustomerName" => $request->customer[$i],
                                "UserID" => $user_id,
                                "GroupID" => $group_id
                            ]
                        );

                        $decode_customer = json_decode($get_customer_id);

                        if ($decode_customer->id) {
                            $customer_id = $decode_customer->data[0]->ID;
                        }

                        //get container id 1
                        $check_container1 = $this->call_api_by_parameter("webGetContainerID",
                            [
                                "ContainerNumber" => $request->container1[$i],
                                "Feet" => $request->feet1[$i],
                                "UserID" => $user_id,
                                "GroupID" => $group_id
                            ]);

                        $decode_insert1 = json_decode($check_container1);

                        if ($decode_insert1->id) {
                            $container1_id = $decode_insert1->data[0]->ID;
                        }

                        //get container id 2
                        $check_container2 = $this->call_api_by_parameter("webGetContainerID",
                            [
                                "ContainerNumber" => $request->container2[$i],
                                "Feet" => $request->feet2[$i],
                                "UserID" => $user_id,
                                "GroupID" => $group_id
                            ]);

                        $decode_insert2 = json_decode($check_container2);

                        if ($decode_insert2->id) {
                            $container2_id = $decode_insert2->data[0]->ID;
                        }

                        $parms_detail = [
                            "TicketID" => $ticket_id,
                            "CustomerID" => $customer_id,
                            "Container1ID" => $container1_id,
                            "Container2ID" => $container2_id,
                            "ReasonID" => $request->reason[$i],
                            "DestinationID" => $request->destination[$i],
                            "Fuel" => $request->fuel[$i],
                            "FuelAdd" => $request->add_more[$i],
                            "DieselReturnAmount" => $request->diesel_return_amount[$i],
                            "TotalFuel" => $request->total_amount[$i],
                            "Note" => $request->note[$i],
                            "StatusNote" => $status_detail,
                            "AdvancePayID" => $request->mtpickup[$i],
                            "PayTripID" => $request->paytrip_id[$i],
                            "PayTrip" => $request->paytrip[$i],
                            "PayTripAdd" => $request->add_cut_paytrip[$i],
                            "PayTripNote" => $request->note_paytrip[$i],
                            "TotalPayTrip" => $request->total_amount_paytrip[$i],
                            "Version" => "V0",
                            "TeamLeader" => $request->team_leader[$i],
                            "ReferenceNumber" => $request->reference_number[$i],
                            "LoloAmount" => $request->lolo[$i],
                            "MTPickUp" => $request->mtpickup_name[$i],
                        ];

                        $this->call_api_by_parameter("webUpdateFleetAvailable", ["ReasonID" => $request->destination[$i], "FleetID" => $request->truck_number]);

                        $call_ticket_detail = $this->call_api_by_parameter("webAddTicketDetail", $parms_detail);

                    }
                }


                if (($amount_mt > 0) && (($count_con1 <= 0) && ($count_con2 <= 0))) {
                    $status = 5;
                } else if (($amount_mt > 0) && (($count_con1 > 0) || ($count_con2 > 0))) {
                    $status = 0;
                } else if (($amount_mt > 0) && (($count_con1 > 0) || ($count_con2 >= 0))) {
                    $status = 0;
                } else if (($amount_mt > 0) && (($count_con1 >= 0) || ($count_con2 > 0))) {
                    $status = 0;
                } else if (($amount_mt == 0) && ($amount_fuel == 0) && ($amount_paytrip > 0)) {
                    $status = 1;
                    $IsOnlyMoney = 1;
                }

                $this->call_api_by_parameter("webUpdateTicketNumber",
                    [
                        "TicketNumber" => $ticket_number,
                        "TicketID" => $ticket_id,
                        "Status" => $status,
                        "IsOnlyMoney" => $IsOnlyMoney,
                        "AmountLolo" => ((-1) * $amount_mt),
                        "AmountPayTrip" => ((-1) * $amount_paytrip),
                        "UserID" => $user_id
                    ]
                );

                $get_ticket_de = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $ticket_id]);

                if ($count_credit > 0) {
                    $update_credit = $this->call_api_by_parameter("webUpdateCreditNote", ["ID" => $credit_id, "TicketID" => $ticket_id]);
                }

                if ($get_ticket_de) {

                    $encode_detail = json_decode($get_ticket_de);

                    if ($encode_detail->id) {

                        $data['data'] = $encode_detail->data;
                        $data['barcode'] = $encode_detail->data[0]->Barcode;
                        $data['img'] = DNS1D::getBarcodePNG($encode_detail->data[0]->Barcode, 'C39');

                        $label = view('pages.backend.ticket.print_new', compact('data'))->render();

                        return response()->json([
                            'error' => 0,
                            'Label' => $label
                        ]);
                    }

                    return response()->json([
                        'error' => "something wrong with your data."
                    ]);
                }

                return response()->json([
                    'error' => 'You are losing your connection. Please check your connection'
                ]);
            }

            return response()->json([
                'error' => "something wrong with your data."
            ]);
        }

        return response()->json([
            'error' => 'You are losing your connection. Please check your connection'
        ]);
    }

    /***
     * Start script for select2 here
     **/

    //this function to get trailer data by filter
    public function get_fleet(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetFleetByFilter", ['PlateNumber' => $term, "UserID" => $user_id]);
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

    //this function to get trailer data by filter
    public function get_driver_by_group(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetDriverByFilter", ['DriverName' => $term, "UserID" => $user_id]);
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

    //this function to get trailer data by filter
    public function get_trailer(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetTrailerByFilter", ['TrailerNumber' => $term, "UserID" => $user_id]);
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

    //this function to get reason data by filter
    public function get_reason(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetReasonByFilter", ['Reason' => $term, "UserID" => $user_id]);
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

    //this function to get reason data by filter
    public function get_mtpickup(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetMTPickupByFilter", ['Name' => $term, "UserID" => $user_id]);
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

    //this function to get reason data by filter
    public function get_destination(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetDestinationByFilter", ['Code' => $term, "UserID" => $user_id]);
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

    //this function to get container data by filter
    public function get_container(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetContainerByFilter", ['ContainerNumber' => $term, "UserID" => $user_id]);
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

    //this function to get get_customer data by filter
    public function get_customer(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetCustomerByFilterGroup", ['CustomerName' => $term, "UserID" => $user_id]);
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

    //this function to get get_team_leader data by filter
    public function get_team_leader(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetTeamLeaderFilterGroup", ['Name' => $term, "UserID" => $user_id]);
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

    //this function to get reason data by filter
    public function get_destination_by_id($id)
    {
        $data_json = $this->call_api_by_parameter("webGetDestinationByID", ['DestinationID' => $id]);

        $json = [];
        if ($data_json) {
            $decode = json_decode($data_json);
            $fuel = 0;
            $total_fuel = 0;
            if ($decode->id) {
                $data = $decode->data[0];
                $fuel = $data->Fuel;
                $total_fuel = $data->Total;
            }
            return response()->json([
                'fuel' => $fuel,
                'total' => $total_fuel,
            ]);

        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function to get autocomplete
    public function autocomplete(Request $request)
    {
        $term = trim($request->value);
        $data_json = $this->call_api_by_parameter("webGetNoteHistory", ['Value' => $term]);

        if (($data_json == false)) {
            return response()->json([
                'con' => true
            ]);
        }

        $decode = json_decode($data_json);
        if ($decode->id) {
            $output = '<ul class="list_note">';
            $text = '';
            foreach ($decode->data as $item) {
                if ($text != $item->Note) {
                    $output .= '<li class="text_note_fuel" data-number="' . $request->number . '">' . $item->Note . '</li>';
                    $text = $item->Note;
                }

            }
            $output .= '</ul>';

            return response()->json([
                'result' => $output,
                "error" => false
            ]);
        }

        return response()->json([
            'error' => "No data."
        ]);
    }

    //this function to get autocomplete
    public function getnote_paytrip(Request $request)
    {
        $term = trim($request->value);
        $data_json = $this->call_api_by_parameter("webGetNotePayHistory", ['Value' => $term]);

        if (($data_json == false)) {
            return response()->json([
                'con' => true
            ]);
        }

        $decode = json_decode($data_json);
        if ($decode->id) {
            $output = '<ul class="list_note">';
            $text = '';
            foreach ($decode->data as $item) {
                if ($text != $item->PayTripNote) {
                    $output .= '<li class="text_note_pay_trip" data-number="' . $request->number . '">' . $item->PayTripNote . '</li>';
                    $text = $item->PayTripNote;
                }

            }
            $output .= '</ul>';

            return response()->json([
                'result' => $output,
                "error" => false
            ]);
        }

        return response()->json([
            'error' => "No data."
        ]);
    }

    //edit ticket
    public function EditTicket($id)
    {

        $user_id = session("ID");

        $data_check = $this->check_permission("SYS022", "edit");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);
        if ($check_menu == false) {
            return view('pages.backend.message');
        }
        $call_api_ticket = $this->call_api_by_parameter("webGetTicketByID", array("TicketID" => $id));
        $call_api_detail = $this->call_api_by_parameter("webGetTicketDetailDataByID", array("TicketID" => $id));

        $encode_ticket = json_decode($call_api_ticket);
        $encode_detail = json_decode($call_api_detail);

        $data['id_ticket'] = $encode_ticket->id;
        $data['list_ticket'] = $encode_ticket->data;
        $data['id_detail'] = $encode_detail->id;
        $data['list_detail'] = $encode_detail->data;

        $data['id'] = $id;

        return view("pages.backend.ticket.edit", compact('data'));
    }

    //do update ticket
    public function update(Request $request)
    {
        $user_id = session("ID");
        $group_id = session("group_id");

        $paytrip = (($request->paytrip_amount_old) - ($request->total_amount_pay_trip));
        $lolo = (($request->lolo_amount_old) - ($request->total_amount_mtpickup));

        $data_ticket = [
            "TicketID" => $request->ticket_id,
            "TicketNumber" => $request->ticket_number,
            "TrailerID" => $request->trailer,
            "PayTrip" => $paytrip,
            "LoloAmount" => $lolo,
            "Status" => $request->status,
            "UserID" => $user_id,
            "TotalFuel" => $request->total_amount_fuel,
            "TotalPayTrip" => $request->total_amount_pay_trip,
        ];

        $call_ticket = $this->call_api_by_parameter("webUpdateTicket", $data_ticket);

        if ($call_ticket) {

            $encode_ticket = json_decode($call_ticket);

            if ($encode_ticket->id) {

                $count_destination = count($request->destination);

                for ($i = 0; $i < $count_destination; $i++) {

                    if ($request->destination[$i] != "") {

                        if ($i === 0) {
                            $status_detail = 0;
                        } else {
                            $status_detail = 1;
                        }

                        //get customer id
                        $get_customer_id = $this->call_api_by_parameter("webGetCustomerID",
                            [
                                "CustomerName" => $request->customer[$i],
                                "UserID" => $user_id,
                                "GroupID" => $group_id
                            ]
                        );

                        $decode_customer = json_decode($get_customer_id);

                        if ($decode_customer->id) {
                            $customer_id = $decode_customer->data[0]->ID;
                        }

                        //get container id 1
                        $check_container1 = $this->call_api_by_parameter("webGetContainerID",
                            [
                                "ContainerNumber" => $request->container1[$i],
                                "Feet" => $request->feet1[$i],
                                "UserID" => $user_id,
                                "GroupID" => $group_id
                            ]);

                        $decode_insert1 = json_decode($check_container1);

                        if ($decode_insert1->id) {
                            $container1_id = $decode_insert1->data[0]->ID;
                        }

                        //get container id 2
                        $check_container2 = $this->call_api_by_parameter("webGetContainerID",
                            [
                                "ContainerNumber" => $request->container2[$i],
                                "Feet" => $request->feet2[$i],
                                "UserID" => $user_id,
                                "GroupID" => $group_id
                            ]);

                        $decode_insert2 = json_decode($check_container2);

                        if ($decode_insert2->id) {
                            $container2_id = $decode_insert2->data[0]->ID;
                        }

                        $parms_detail = [
                            "TicketID" => $request->ticket_id,
                            "CustomerID" => $customer_id,
                            "Container1ID" => $container1_id,
                            "Container2ID" => $container2_id,
                            "ReasonID" => $request->reason[$i],
                            "DestinationID" => $request->destination[$i],
                            "Fuel" => $request->fuel[$i],
                            "FuelAdd" => $request->add_more[$i],
                            "DieselReturnAmount" => $request->diesel_return_amount[$i],
                            "TotalFuel" => $request->total_amount[$i],
                            "Note" => $request->note[$i],
                            "StatusNote" => $status_detail,
                            "AdvancePayID" => $request->mtpickup[$i],
                            "PayTripID" => $request->paytrip_id[$i],
                            "PayTrip" => $request->paytrip[$i],
                            "PayTripAdd" => $request->add_cut_paytrip[$i],
                            "PayTripNote" => $request->note_paytrip[$i],
                            "TotalPayTrip" => $request->total_amount_paytrip[$i],
                            "Version" => "V0",
                            "TeamLeader" => $request->team_leader[$i],
                            "ReferenceNumber" => $request->reference_number[$i],
                            "LoloAmount" => $request->lolo[$i],
                            "MTPickUp" => $request->mtpickup_name[$i],
                            "TicketDetailID" => $request->ticket_detail_id[$i],
                            "Status" => $request->old_status,
                            "TrailerID" => $request->old_trailer_id,
                            "UserID" => $user_id
                        ];

                        $this->call_api_by_parameter("webUpdateFleetAvailable", ["ReasonID" => $request->destination[$i], "FleetID" => $request->truck_number]);

                        $call_ticket_detail = $this->call_api_by_parameter("webUpdateTicketDetail", $parms_detail);
//                        dd($call_ticket_detail);

                    }
                }

                $get_ticket_de = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $request->ticket_id]);

                if ($get_ticket_de) {

                    $encode_detail = json_decode($get_ticket_de);

                    if ($encode_detail->id) {

                        $data['data'] = $encode_detail->data;
                        $data['barcode'] = $encode_detail->data[0]->Barcode;
                        $data['img'] = DNS1D::getBarcodePNG($encode_detail->data[0]->Barcode, 'C39');

                        $label = view('pages.backend.ticket.print_new', compact('data'))->render();

                        return response()->json([
                            'error' => 0,
                            'Label' => $label,
                        ]);
                    }

                    return response()->json([
                        'error' => "something wrong with your data."
                    ]);
                }
            }

            return response()->json([
                'error' => "something wrong with your data."
            ]);
        }

        return response()->json([
            'error' => 'You are losing your connection. Please check your connection'
        ]);


    }

    //this function for testing
    public function Testing($id)
    {

        $get_ticket_de = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $id]);
        $encode_detail = json_decode($get_ticket_de);
        $data['data'] = $encode_detail->data;
        $data['barcode'] = $encode_detail->data[0]->Barcode;
        $data['img'] = DNS1D::getBarcodePNG($encode_detail->data[0]->Barcode, 'C39');

        return view("pages.backend.ticket.testing", compact('data'));
    }

    public function get_reference($id)
    {
        $ticet_detail = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $id]);

        if ($ticet_detail == false) {
            return response()->json([
                'error' => "You are losing your connection."
            ]);
        }

        $decode = json_decode($ticet_detail);

        $data['list'] = $decode->data;
        $data['row'] = $decode->data[0];
        $data['id_list'] = $decode->id;

        $result = view("pages.backend.ticket.reference", compact('data'))->render();

        return response()->json([
            'result' => $result,
            'volume' => $decode->data[0]->TotalAmountFuel
        ]);
    }

}