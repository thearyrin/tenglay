<?php

namespace App\Http\Controllers\Admin\WriteOff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class WriteOffController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('logout');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function request_list(Request $request)
    {
        $data_check = $this->check_permission("SYS024", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $request->session()->forget([
            'date_start_request_writeoff',
            'date_end_request_writeoff',
            'ticket_id_request_writeoff',
            'ticket_number_request_writeoff',
            'status_request_writeoff',
            'from_date_request_writeoff',
            'to_date_request_writeoff',
            'search_request_writeoff',
            'fleet_id_request_writeoff',
            'fleet_name_request_writeoff',
        ]);

        $date_start = date("d/m/Y");
        $date_end = date("d/m/Y");

        $ticket_id = -1;
        $ticket_number = "All";
        $fleet_id = -1;
        $fleet_name = "All";
        $status = 0;
        $search = 0;

        if ($request->isMethod('post')) {

            $ticket_id = $request->ticket_id;
            $ticket_number = $request->ticket_number;
            $status = $request->status;
            $search = 1;
            $date_start = $request->from_date;
            $date_end = $request->to_date;
            $fleet_id = $request->fleet_id;
            $fleet_name = $request->fleet_name;
        }

        $from_date = Carbon::createFromFormat("d/m/Y", $date_start)->format("d-M-Y");
        $to_date = Carbon::createFromFormat("d/m/Y", $date_end)->format("d-M-Y");

        $parms = [
            "FromDate" => $from_date,
            "ToDate" => $to_date,
            "TicketID" => $ticket_id,
            "Status" => $status,
            "PlateNumber" => $fleet_id,
        ];

        $get_writeoff = $this->call_api_by_parameter("webGetWriteOff", $parms);

        $decode_writeoff = json_decode($get_writeoff);

        $data['user_id'] = $user_id;
        $data['search'] = $search;
        $data['from_date'] = $date_start;
        $data['to_date'] = $date_end;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['status'] = $status;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;

        $data['id_request'] = $decode_writeoff->id;
        $data['list_request'] = $decode_writeoff->data;

        $data['edit'] = CheckPemission($this->check_permission("SYS024", "edit"));

        session([
            "date_start_request_writeoff" => $from_date,
            "date_end_request_writeoff" => $to_date,
            "ticket_id_request_writeoff" => $ticket_id,
            "ticket_number_request_writeoff" => $ticket_number,
            "status_request_writeoff" => $status,
            "from_date_request_writeoff" => $date_start,
            "to_date_request_writeoff" => $date_end,
            "search_request_writeoff" => $search,
            "fleet_id_request_writeoff" => $fleet_id,
            "fleet_name_request_writeoff" => $fleet_name,
        ]);

        return view('pages.backend.writeoff.show_request', compact('data'));
    }

    //this function for data after list
    public function data_request()
    {

        $data_check = $this->check_permission("SYS024", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $date_start = session("date_start_request_writeoff");
        $date_end = session("date_end_request_writeoff");
        $ticket_id = session("ticket_id_request_writeoff");
        $ticket_number = session("ticket_number_request_writeoff");
        $status = session("status_request_writeoff");
        $fleet_id = session("fleet_id_request_writeoff");
        $fleet_name = session("fleet_name_request_writeoff");
        $from_date = session("from_date_request_writeoff");
        $to_date = session("to_date_request_writeoff");
        $search = session("search_request_writeoff");

        $user_id = session("ID");

        $parms = [
            "FromDate" => $date_start,
            "ToDate" => $date_end,
            "TicketID" => $ticket_id,
            "Status" => $status,
            "PlateNumber" => $fleet_id,
        ];

        $get_writeoff = $this->call_api_by_parameter("webGetWriteOff", $parms);

//        $call_plate = $this->call_api_by_parameter("webGetFleetInWriteOff", ["UserID" => $user_id]);

        $decode_writeoff = json_decode($get_writeoff);
//        $decode_plate = json_decode($call_plate);

        $data['user_id'] = $user_id;
        $data['search'] = $search;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['status'] = $status;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;

        $data['id_request'] = $decode_writeoff->id;
        $data['list_request'] = $decode_writeoff->data;
//        $data['id_fleet'] = $decode_plate->id;
//        $data['list_fleet'] = $decode_plate->data;
        $data['edit'] = CheckPemission($this->check_permission("SYS024", "edit"));

        return view('pages.backend.writeoff.show_request', compact('data'));
    }

    /**
     * Export data to excel
     */
    public function export_request()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $from_date = Session::get("date_start_request");
        $to_date = Session::get("date_end_request");
        $ticket_id = Session::get("ticket_id_request");
        $status = Session::get("status_request");
        $fleet_id = Session::get("fleet_id_request");

        if ($status == 3) {
            $status_id = 'all';
        } else {
            $status_id = $status;
        }

        $parms = [
            "FromDate" => $from_date,
            "ToDate" => $to_date,
            "TicketID" => $ticket_id,
            "Status" => $status_id,
            "PlateNumber" => $fleet_id,
        ];

        $get_writeoff = $this->call_api_by_parameter("webGetWriteOff", $parms);

        $write_off = array();
        $write_off[] = ['ID', 'Request Date', 'Request By', 'Approve Date', 'Approve By', 'Ticket No', 'Plate Number', 'Driver ID', 'Driver Name', 'Amount(L)', 'Remark', "Status"];

        if ($get_writeoff == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        } else {
            $data_json = json_decode($get_writeoff);
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

                    $write_off[] = array($row->ID, $row->RequestDate, $row->RequestName, $row->ApproveDate, $row->ApproveName, $row->TicketNo, $row->PlateNumber, $row->CodeID, $row->NameKh, $row->Amount . " L", $row->Reason, $status);
                    $i++;
                }
            }

            // and append it to the payments array.
            return \Excel::create('Write Off List Report At ' . $from_date . '-To-' . $to_date, function ($excel) use ($write_off, $i) {
                $excel->sheet('sheet name', function ($sheet) use ($write_off, $i) {

                    $sheet->cells('A1:L1', function ($cells) {
                        $cells->setAlignment('center');
                        $cells->setFontWeight("bold");
                        $cells->setFontColor('#000000');
                    });
                    $sheet->fromArray($write_off, null, 'A1', false, false);

                });
            })->download('xlsx');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_request()
    {
        $data_check = $this->check_permission("SYS024", "add");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $call_plate_number = $this->call_api_by_parameter("webGetFleetInTicketUsed", ["UserID" => $user_id]);

        if ($call_plate_number == false) {
            return redirect('/admin')->withErrors("You are losing your connection");
        }

        $decode_fleet = json_decode($call_plate_number);
        $data['user_id'] = $user_id;
        $data['id_fleet'] = $decode_fleet->id;
        $data['data_fleet'] = $decode_fleet->data;

        return view('pages.backend.writeoff.create', compact('data'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_ticket_number(Request $request)
    {
        $user_id = Session::get("ID");

        $call_api = $this->call_api_by_parameter("webGetTicketNoWriteOff", [
            "UserID" => $user_id,
            "PlateNumber" => $request->fleet_id,
            "TicketNumber" => ""
        ]);

        if ($call_api) {

            $decode_json = json_decode($call_api);

            return response()->json([
                'id' => $decode_json->id,
                'list' => $decode_json->data
            ]);
        }
        return response()->json([
            'error' => "You are losing your connection."
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_fleet_in_ticket(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $call_api = $this->call_api_by_parameter("webGetFleetInTicketUsed", ["UserID" => $user_id, "PlateNumber" => $term]);
        $json = [];
        if ($call_api) {
            $decode = json_decode($call_api);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }
        return response()->json([
            'error' => "You are losing your connection."
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_ticket_number_filter(Request $request)
    {
        $user_id = Session::get("ID");
        $call_api = $this->call_api_by_parameter("webGetTicketNoWriteOff",
            [
                "UserID" => $user_id,
                "PlateNumber" => $request->fleet_id,
                "TicketNumber" => trim($request->q)
            ]
        );

        $json = [];
        if ($call_api) {
            $decode = json_decode($call_api);
            if ($decode->id) {
                return response()->json($decode->data);
            } else {
                return response()->json($json);
            }
        }
        return response()->json([
            'error' => "You are losing your connection."
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $parm = [
            "WriteOffID" => $request->id,
            "Status" => $request->status,
            "Text" => $request->text
        ];

        $call_update = $this->call_api_by_parameter("webUpdateWriteOff", $parm);

        if ($call_update == false) {
            return response()->json([
                "error" => "You are losing your connection."
            ]);
        }

        $decode_json = json_decode($call_update);

        if ($decode_json->id) {
            return response()->json([
                "error" => 0
            ]);
        }
        return response()->json([
            "error" => "Something wrong with your data"
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function approve_list(Request $request)
    {
        $data_check = $this->check_permission("SYS025", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $request->session()->forget([
            "date_start_writeoff_approve",
            "date_end_writeoff_approve",
            "ticket_id_writeoff_approve",
            "ticket_number_writeoff_approve",
            "status_writeoff_approve",
            "from_date_writeoff_approve",
            "to_date_writeoff_approve",
            "search_writeoff_approve",
            "fleet_id_writeoff_approve",
            "fleet_name_writeoff_approve",
        ]);

        $date_start = date("d/m/Y");
        $date_end = date("d/m/Y");

        $ticket_id = '-1';
        $ticket_number = "All";
        $status = 0;
        $search = 0;
        $fleet_id = -1;
        $fleet_name = "All";

        if ($request->isMethod('post')) {

            $ticket_id = $request->ticket_id;
            $ticket_number = $request->ticket_number;
            $status = $request->status;
            $search = 1;
            $date_start = $request->from_date;
            $date_end = $request->to_date;
            $fleet_id = $request->fleet_id;
            $fleet_name = $request->fleet_name;
        }

        $from_date = Carbon::createFromFormat("d/m/Y", $date_start)->format("d-M-Y");
        $to_date = Carbon::createFromFormat("d/m/Y", $date_end)->format("d-M-Y");

        $parms = [
            "FromDate" => $from_date,
            "ToDate" => $to_date,
            "TicketID" => $ticket_id,
            "Status" => $status,
            "PlateNumber" => $fleet_id,
        ];

        $get_writeoff = $this->call_api_by_parameter("webGetWriteOff", $parms);


        $decode_writeoff = json_decode($get_writeoff);

        $data['user_id'] = $user_id;
        $data['search'] = $search;
        $data['from_date'] = $date_start;
        $data['to_date'] = $date_end;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['status'] = $status;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;

        $data['id_request'] = $decode_writeoff->id;
        $data['list_request'] = $decode_writeoff->data;

        session([
            "date_start_writeoff_approve" => $from_date,
            "date_end_writeoff_approve" => $to_date,
            "ticket_id_writeoff_approve" => $ticket_id,
            "ticket_number_writeoff_approve" => $ticket_number,
            "status_writeoff_approve" => $status,
            "from_date_writeoff_approve" => $date_start,
            "to_date_writeoff_approve" => $date_end,
            "search_writeoff_approve" => $search,
            "fleet_id_writeoff_approve" => $fleet_id,
            "fleet_name_writeoff_approve" => $fleet_name,
        ]);

        $data['add'] = CheckPemission($this->check_permission("SYS025", "add"));

        return view('pages.backend.writeoff.show_approve', compact('data'));
    }


    //this function for keeping data after reloading
    public function data_approve()
    {
        $data_check = $this->check_permission("SYS025", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $date_start = Session::get("date_start_writeoff_approve");
        $date_end = Session::get("date_end_writeoff_approve");
        $ticket_id = Session::get("ticket_id_writeoff_approve");
        $ticket_number = Session::get("ticket_number_writeoff_approve");
        $status = Session::get("status_writeoff_approve");
        $from_date = Session::get("from_date_writeoff_approve");
        $to_date = Session::get("to_date_writeoff_approve");
        $search = Session::get("search_writeoff_approve");
        $fleet_id = Session::get("fleet_id_writeoff_approve");
        $fleet_name = Session::get("fleet_name_writeoff_approve");

        $user_id = Session::get("ID");

        $parms = [
            "FromDate" => $date_start,
            "ToDate" => $date_end,
            "TicketID" => $ticket_id,
            "Status" => $status,
            "PlateNumber" => $fleet_id,
        ];

        $get_writeoff = $this->call_api_by_parameter("webGetWriteOff", $parms);

        $decode_writeoff = json_decode($get_writeoff);

        $data['user_id'] = $user_id;
        $data['search'] = $search;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['status'] = $status;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;

        $data['id_request'] = $decode_writeoff->id;
        $data['list_request'] = $decode_writeoff->data;

        $data['add'] = CheckPemission($this->check_permission("SYS025", "add"));

        return view('pages.backend.writeoff.show_approve', compact('data'));
    }

    /**
     * Export data to excel
     */
    public function export_approve()
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $from_date = Session::get("date_start_approve");
        $to_date = Session::get("date_end_approve");
        $ticket_id = Session::get("ticket_id_approve");
        $status = Session::get("status_approve");
        $fleet_id = Session::get("fleet_id_approve");

        if ($status == 3) {
            $status_id = 'all';
        } else {
            $status_id = $status;
        }

        $parms = [
            "FromDate" => $from_date,
            "ToDate" => $to_date,
            "TicketID" => $ticket_id,
            "Status" => $status_id,
            "PlateNumber" => $fleet_id,
        ];

        $get_writeoff = $this->call_api_by_parameter("webGetWriteOff", $parms);

        $write_off = array();
        $write_off[] = ['ID', 'Request Date', 'Request By', 'Approve Date', 'Approve By', 'Ticket No', 'Plate Number', 'Driver ID', 'Driver Name', 'Amount(L)', 'Remark', "Status"];

        if ($get_writeoff == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        } else {
            $data_json = json_decode($get_writeoff);
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

                    $write_off[] = array($row->ID, $row->RequestDate, $row->RequestName, $row->ApproveDate, $row->ApproveName, $row->TicketNo, $row->PlateNumber, $row->CodeID, $row->NameKh, $row->Amount . " L", $row->Reason, $status);
                    $i++;
                }
            }

            // and append it to the payments array.
            return \Excel::create('Write Off List Report At ' . $from_date . '-To-' . $to_date, function ($excel) use ($write_off, $i) {
                $excel->sheet('sheet name', function ($sheet) use ($write_off, $i) {

                    $sheet->cells('A1:L1', function ($cells) {
                        $cells->setAlignment('center');
                        $cells->setFontWeight("bold");
                        $cells->setFontColor('#000000');
                    });

                    $sheet->fromArray($write_off, null, 'A1', false, false);
                });
            })->download('xlsx');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function do_approve($id)
    {

        $parms = [
            "ID" => $id,
            "ApproveID" => Session::get("ID"),
            "DateApprove" => date("d-M-Y h:i A")
        ];

        $call_approve = $this->call_api_by_parameter("webApproveWriteOff", $parms);

        if ($call_approve) {
            return response()->json([
                'error' => 1,
                'msg' => "Success"
            ]);
        }
        return redirect('/admin')->withErrors("You are losing your connection");
    }

    /**
     * this function to approve multiple
     **/
    public function approve_multiple(Request $request)
    {
        foreach ($request->id as $id) {
            $parms = [
                "ID" => $id,
                "ApproveID" => Session::get("ID"),
                "DateApprove" => date("d-M-Y h:i A")
            ];

            $call_approve = $this->call_api_by_parameter("webApproveWriteOff", $parms);
        }

        if ($call_approve) {
            return response()->json([
                'error' => 1,
                'msg' => "Success"
            ]);
        }
        return redirect('/admin')->withErrors("You are losing your connection");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function request_store(Request $request)
    {
//        dd($request->all());
        $user_id = Session::get("ID");

        $call_writeoffe = $this->call_api_by_parameter("webGetWriteOffLastNumber", ["UserID" => $user_id]);

        if ($call_writeoffe == false) {
            return response()->json([
                'error' => "You are losing your connection."
            ]);
        }

        $decode_write = json_decode($call_writeoffe);

        $write_number = '';

        if ($decode_write->id) {
            $write_number = str_pad($decode_write->data[0]->WriteOffNumber + 1, 8, '0', STR_PAD_LEFT);
        } else {
            $write_number = str_pad(1, 8, '0', STR_PAD_LEFT);
        }

        $count_ticket = count($request->ticket_id);

        for ($i = 0; $i < $count_ticket; $i++) {

            $parms = [
                "TicketID" => $request->ticket_id[$i],
                "RequestID" => $user_id,
                "Amount" => $request->amount_fuel[$i],
                "Reason" => $request->remark_writeoff[$i],
                "Status" => 0,
                "RequestDate" => date("d-M-Y h:i A"),
                "WriteOffNumber" => $write_number,
                "LoloAmount" => $request->lolo_amount_writeoff[$i],
                "PayTripAmount" => $request->paytrip_amount_writeoff[$i]
            ];

            $add_writeoff = $this->call_api_by_parameter("webAddWriteOff", $parms);
        }

        $call_data_write = $this->call_api_by_parameter("webGetWriteOffByNumber", ["WriteOffNumber" => $write_number]);

        if ($call_data_write) {

            $decode_write_off = json_decode($call_data_write);

            if ($decode_write_off->id) {

                $id = [];
                $list = [];
                foreach ($decode_write_off->data as $item) {
                    $call[$item->TicketID] = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $item->TicketID]);

                    $decode_ticket[$item->TicketID] = json_decode($call[$item->TicketID]);
                    $id[$item->TicketID] = $decode_ticket[$item->TicketID]->id;
                    $list[$item->TicketID] = $decode_ticket[$item->TicketID]->data;
                }

                $data['list'] = $decode_write_off->data;
                $data['list_ticket'] = $list;
                $data['id_ticket'] = $id;

                $label = view('pages.backend.writeoff.print', compact('data'))->render();

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
            'error' => "You are losing your connection."
        ]);
    }

    //this function for reprint
    public function RePrint(Request $request)
    {
        $call_data_write = $this->call_api_by_parameter("webGetWriteOffByNumber", ["WriteOffNumber" => $request->id]);

        if ($call_data_write) {

            $decode_write_off = json_decode($call_data_write);

            if ($decode_write_off->id) {

                $id = [];
                $list = [];
                foreach ($decode_write_off->data as $item) {
                    $call[$item->TicketID] = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $item->TicketID]);
                    $decode_ticket[$item->TicketID] = json_decode($call[$item->TicketID]);
                    $id[$item->TicketID] = $decode_ticket[$item->TicketID]->id;
                    $list[$item->TicketID] = $decode_ticket[$item->TicketID]->data;
//                    dd($id[$item->TicketID]);
                }

//                dd($id);
                $data['list'] = $decode_write_off->data;
                $data['list_ticket'] = $list;
                $data['id_ticket'] = $id;

                $label = view('pages.backend.writeoff.print', compact('data'))->render();

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
            'error' => "You are losing your connection."
        ]);
    }

}
