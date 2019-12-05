<?php

namespace App\Http\Controllers\Admin\Round;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class RoundTripController extends Controller
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
    public function index(Request $request)
    {
        $data_check = $this->check_permission("SYS026", "view");

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

        Session::forget("from_date_round");
        Session::forget("to_date_round");
        Session::forget("fleet_id_round");
        Session::forget("fleet_name_round");
        Session::forget("ticket_id_round");
        Session::forget("ticket_number_round");
        Session::forget("search_round");

        $ticket_number = 'All';
        $fleet_name = 'All';
        $ticket_id = "-1";
        $fleet_id = "-1";
        $roundtrip_id = "All";

        if ($request->isMethod('post')) {

            $ticket_id = $request->ticket_id;
            $ticket_number = $request->ticket_number;
            $fleet_id = $request->plate_number;
            $fleet_name = $request->fleet_name;

            $date_start = $request->from_date;
            $date_end = $request->to_date;
            $roundtrip_id = $request->roundtrip_id;

            $search = 1;
        }

        $from_date = Carbon::createFromFormat("d/m/Y", $date_start)->format("d-M-Y");
        $to_date = Carbon::createFromFormat("d/m/Y", $date_end)->format("d-M-Y");

        $parms = [
            "UserID" => $user_id,
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "TicketNumber" => $ticket_id,
            "TruckNumber" => $fleet_id,
            "RoundTripID" => $roundtrip_id
        ];

        $call_api_list = $this->call_api_by_parameter("webGetRoundTripList", $parms);
        $call_round = $this->call_api_by_parameter("webGetRoundTripFilterInList", ["UserID" => $user_id]);

        $decode_list = json_decode($call_api_list);
        $decode_round = json_decode($call_round);

        $data['id'] = $decode_list->id;
        $data['list'] = $decode_list->data;
        $data['search'] = $search;
        $data['from_date'] = $date_start;
        $data['to_date'] = $date_end;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;
        $data['round_id'] = $roundtrip_id;
        $data['round_data_id'] = $decode_round->id;
        $data['round_data_list'] = $decode_round->data;

        $data['edit_per'] = CheckPemission($this->check_permission("SYS026", "edit"));

        Session::put("from_date_round", $from_date);
        Session::put("to_date_round", $to_date);
        Session::put("date_start_round", $date_start);
        Session::put("date_end_round", $date_end);
        Session::put("fleet_id_round", $fleet_id);
        Session::put("fleet_name_round", $fleet_name);
        Session::put("ticket_id_round", $ticket_id);
        Session::put("ticket_number_round", $ticket_number);
        Session::put("search_round", $search);
        Session::put("roundtrip_list", $roundtrip_id);

        return view("pages.backend.round.show", compact('data'));
    }

    public function round_list()
    {
        $from_date = Session::get("from_date_round");
        $to_date = Session::get("to_date_round");
        $date_start = Session::get("date_start_round");
        $date_end = Session::get("date_end_round");
        $fleet_id = Session::get("fleet_id_round");
        $fleet_name = Session::get("fleet_name_round");
        $ticket_id = Session::get("ticket_id_round");
        $ticket_number = Session::get("ticket_number_round");
        $search = Session::get("search_round");
        $roundtrip_id = Session::get("roundtrip_list");

        $data_check = $this->check_permission("SYS026", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $parms = [
            "UserID" => $user_id,
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "TicketNumber" => $ticket_id,
            "TruckNumber" => $fleet_id,
            "RoundTripID" => $roundtrip_id
        ];

        $call_api_list = $this->call_api_by_parameter("webGetRoundTripList", $parms);
        $call_round = $this->call_api_by_parameter("webGetRoundTripFilterInList", ["UserID" => $user_id]);
        $decode_list = json_decode($call_api_list);
        $decode_round = json_decode($call_round);

        $data['id'] = $decode_list->id;
        $data['list'] = $decode_list->data;
        $data['search'] = $search;
        $data['from_date'] = $date_start;
        $data['to_date'] = $date_end;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;
        $data['round_id'] = $roundtrip_id;
        $data['round_data_id'] = $decode_round->id;
        $data['round_data_list'] = $decode_round->data;

        $data['edit_per'] = CheckPemission($this->check_permission("SYS026", "edit"));

        return view("pages.backend.round.show", compact('data'));
    }

    //this function for export round trip list
    public function ExportList()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $user_id = Session::get("ID");

        $from_date = Session::get("from_date_round");
        $to_date = Session::get("to_date_round");
        $fleet = Session::get("fleet_id_round");
        $ticket = Session::get("ticket_id_round");
        $roundtrip_id = Session::get("roundtrip_list");

        $parms = [
            "UserID" => $user_id,
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "TicketNumber" => $ticket,
            "TruckNumber" => $fleet,
            "RoundTripID" => $roundtrip_id,
        ];
        $call_api_list = $this->call_api_by_parameter("webGetRoundTripListExport", $parms);
//        dd($call_api_list);

        $round_trip = ['ID', 'DateTime', 'Ticket Number', 'RoundTrip', 'Type', 'Truck Number', 'Trailer Number',
            'Driver', 'Driver ID', 'Container1', 'Feet1', 'Container2', 'Feet2', 'Customer1', 'Customer2', 'Destination', 'Delivery Location',
            'Remark'];
        if ($call_api_list == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        } else {

            $data_json = json_decode($call_api_list);
            $data = [];

            if ($data_json->id) {

                $data = json_encode($data_json->data);
                $data = json_decode($data, true);
            }

            // and append it to the payments array.
            return Excel::create('Round Trip List Return Report At ' . $from_date . '-To-' . $to_date, function ($excel) use ($data, $round_trip) {

                $excel->sheet('Ticket Sheet', function ($sheet) use ($data, $round_trip) {

                    $sheet->fromArray($data, null, 'A1', true);

//                    $sheet->row(1, $round_trip);

                    $sheet->getStyle('A1:R1')->applyFromArray(array(
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

                });
            })->download('xlsx');
        }
    }

    //this function for getting data ticket number
    public function getTicketNumber(Request $request)
    {
        $term = trim($request->q);
        $user_id = Session::get("ID");

        $call_api_tk = $this->call_api_by_parameter("webGetTicketRoundTrip", ["UserID" => $user_id, "TicketNumber" => $term]);

        $json = [];
        if ($call_api_tk) {
            $decode = json_decode($call_api_tk);
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

    //this function for getting data ticket number
    public function get_fleet_in_round_trip(Request $request)
    {
        $term = trim($request->q);
        $user_id = Session::get("ID");

        $call_api_tk = $this->call_api_by_parameter("webGetFleetRoundTrip", ["UserID" => $user_id, "FleetNumber" => $term]);

        $json = [];
        if ($call_api_tk) {
            $decode = json_decode($call_api_tk);
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

    //this function for getting data ticket number
    public function EditRoundTrip(Request $request)
    {
        $user_id = Session::get("ID");

        $call_api = $this->call_api_by_parameter("webGetRoundTripByID", ["ID" => $request->id]);

        if ($call_api) {

            $decode = json_decode($call_api);

            if ($decode->id) {

                $data['row'] = $decode->data[0];
                $data['ticket'] = $request->ticket;
                $data['id'] = $request->id;
                $result = view("pages.backend.round.edit", compact('data'))->render();

                return response()->json([
                    'result' => $result
                ]);
            }
            return response()->json([
                'result' => "No Data found"
            ]);

        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    //this function for getting data ticket number
    public function get_data_type(Request $request)
    {
        $user_id = Session::get("ID");

        $call_api = $this->call_api_by_parameter("webGetRoundTripByID", ["ID" => $request->id]);

        if ($call_api) {

            $decode = json_decode($call_api);

            if ($decode->id) {

                $call_trailer = $this->call_api_by_parameter("webGetTrailer", ["UserID" => $user_id]);
                $call_destination = $this->call_api_by_parameter("webGetDestination", ["UserID" => $user_id]);
                $call_customer = $this->call_api_by_parameter("webGetCustomer", ["UserID" => $user_id]);

                $decode_customer = json_decode($call_customer);
                $decode_trailer = json_decode($call_trailer);
                $decode_des = json_decode($call_destination);

                $id_trailer = $decode_trailer->id;
                $list_trailer = $decode_trailer->data;
                $id_destination = $decode_des->id;
                $list_destination = $decode_des->data;
                $id_customer = $decode_customer->id;
                $list_customer = $decode_customer->data;

                $data['id_destination'] = $id_destination;
                $data['list_destination'] = $list_destination;

                $data['id_trailer'] = $id_trailer;
                $data['list_trailer'] = $list_trailer;

                $data['id_customer'] = $id_customer;
                $data['list_customer'] = $list_customer;


                $data['row'] = $decode->data[0];
                $data['status'] = $request->status;
                $result = view("pages.backend.round.data_edit", compact('data'))->render();

                return response()->json([
                    'result' => $result
                ]);
            }
            return response()->json([
                'result' => "No Data found"
            ]);

        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data_check = $this->check_permission("SYS026", "add");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");
        $date_start = '';
        $date_end = '';
        $from_date = '';
        $to_date = '';
        $search = 0;
        $round_id = "All";

        $request->session()->forget([
            "from_date_round_create",
            "to_date_round_create",
            "fleet_round_create",
            "ticket_round_create",
            "date_start_round_trip",
            "date_end_round_trip",
            "search_round_trip",
            "fleet_name_round_create",
            "ticket_number_round_create",
        ]);

        $ticket_id = '-1';
        $fleet_id = '-1';
        $ticket_number = "All";
        $fleet_name = "All";

        if ($request->isMethod('post')) {

            $ticket_id = $request->ticket_id;
            $ticket_number = $request->ticket_number;
            $fleet_id = $request->fleet_id;
            $fleet_name = $request->fleet_name;
            $date_start = $request->from_date;
            $date_end = $request->to_date;
            $round_id = $request->rountrip_id;
            $search = 1;
        }

        if ($date_start != '') {
            $from_date = Carbon::createFromFormat("d/m/Y", $date_start)->format("d-M-Y");
        }

        if ($date_end != '') {
            $to_date = Carbon::createFromFormat("d/m/Y", $date_end)->format("d-M-Y");
        }

        $parms = [
            "UserID" => $user_id,
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "TicketNumber" => $ticket_id,
            "TruckNumber" => $fleet_id,
            "RoundID" => $round_id
        ];

        $call_api_list = $this->call_api_by_parameter("webGetTicketByPort", $parms);
        $call_roundtrip = $this->call_api_by_parameter("webGetRoundTripFilterInCreate", ["UserID" => $user_id]);

        $decode_list = json_decode($call_api_list);
        $decode_round = json_decode($call_roundtrip);

        $data['id'] = $decode_list->id;
        $data['list'] = $decode_list->data;
        $data['search'] = $search;
        $data['from_date'] = $date_start;
        $data['to_date'] = $date_end;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;
        $data['round_id'] = $round_id;
        $data["round_data_id"] = $decode_round->id;
        $data['round_data_list'] = $decode_round->data;

        session([
            "from_date_round_create" => $from_date,
            "to_date_round_create" => $to_date,
            "fleet_round_create" => $fleet_id,
            "fleet_name_round_create" => $fleet_name,
            "ticket_round_create" => $ticket_id,
            "ticket_number_round_create" => $ticket_number,
            "date_start_round_trip" => $date_start,
            "date_end_round_trip" => $date_end,
            "search_round_trip" => $search,
            "create_round" => $round_id,
        ]);

        return view("pages.backend.round.create", compact('data'));
    }

    //this function for data create round trip
    public function create_round()
    {
        $data_check = $this->check_permission("SYS026", "add");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");
        $from_date = Session::get("from_date_round_create");
        $to_date = Session::get("to_date_round_create");
        $fleet_id = Session::get("fleet_round_create");
        $ticket_id = Session::get("ticket_round_create");
        $date_start = Session::get("date_start_round_trip");
        $date_end = Session::get("date_end_round_trip");
        $search = Session::get("search_round_trip");
        $fleet_name = session::get("fleet_name_round_create");
        $ticket_number = session::get("ticket_number_round_create");
        $round_id = session::get("create_round");

        $parms = [
            "UserID" => $user_id,
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "TicketNumber" => $ticket_id,
            "TruckNumber" => $fleet_id,
            "RoundID" => $round_id
        ];

        $call_api_list = $this->call_api_by_parameter("webGetTicketByPort", $parms);
        $call_roundtrip = $this->call_api_by_parameter("webGetRoundTripFilterInCreate", ["UserID" => $user_id]);

        $decode_list = json_decode($call_api_list);
        $decode_round = json_decode($call_roundtrip);

        $data['id'] = $decode_list->id;
        $data['list'] = $decode_list->data;
        $data['search'] = $search;
        $data['from_date'] = $date_start;
        $data['to_date'] = $date_end;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;
        $data['round_id'] = $round_id;
        $data["round_data_id"] = $decode_round->id;
        $data['round_data_list'] = $decode_round->data;

        return view("pages.backend.round.create", compact('data'));
    }

    //this function for export data create roundtrip
    public function ExportCreate()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $user_id = Session::get("ID");
        $from_date = Session::get("from_date_round_create");
        $to_date = Session::get("to_date_round_create");
        $fleet_id = Session::get("fleet_round_create");
        $ticket_id = Session::get("ticket_round_create");

        $parms = [
            "UserID" => $user_id,
            "DateStart" => $from_date,
            "DateEnd" => $to_date,
            "TicketNumber" => $ticket_id,
            "TruckNumber" => $fleet_id,
        ];

        $call_api_list = $this->call_api_by_parameter("webGetTicketByPort", $parms);

        $round_trip = array();

        $round_trip[] = ['Ticket Number', 'DateTime', 'Issued By', 'Truck Number', 'Driver Name', 'Trailer Number',
            'Purpose', 'Destination', 'Fuel(L)', 'Status'];

        if ($call_api_list == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        } else {

            $data_json = json_decode($call_api_list);
            if ($data_json->id) {
                foreach ($data_json->data as $row) {
                    $round_trip[] = array($row->TicketNo, $row->issue_date_time, $row->Issuer, $row->PlateNumber, $row->NameKh,
                        $row->TrailerNumber, $row->Reason, $row->Code, $row->TotalFuel, 'No Return');
                }
            }

            // and append it to the payments array.
            return \Excel::create('Round Trip Create Return Report At ' . $from_date . '-To-' . $to_date, function ($excel) use ($round_trip) {
                $excel->sheet('sheet name', function ($sheet) use ($round_trip) {

                    $sheet->cells('A1:J1', function ($cells) {
                        $cells->setAlignment('center');
                        $cells->setFontWeight("bold");
                        $cells->setFontColor('#000000');
                    });

                    $sheet->fromArray($round_trip, null, 'A1', false, false);
                });
            })->download('xlsx');
        }

    }

    //this function for getting data ticket number
    public function getTicketNumberNoReturn(Request $request)
    {
        $term = trim($request->q);
        $user_id = Session::get("ID");

        $call_api_tk = $this->call_api_by_parameter("webGetTicketNoRoundTrip", ["UserID" => $user_id, "TicketNumber" => $term]);

        $json = [];
        if ($call_api_tk) {
            $decode = json_decode($call_api_tk);
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

    //this function for getting data fleet number
    public function getFleetNumberNoReturn(Request $request)
    {
        $term = trim($request->q);
        $user_id = Session::get("ID");

        $call_api_tk = $this->call_api_by_parameter("webGetFleetNoRoundTrip", ["UserID" => $user_id, "PlateNumber" => $term]);

        $json = [];
        if ($call_api_tk) {
            $decode = json_decode($call_api_tk);
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

    /**
     * Display the specified resource.
     *
     * @param  get
     * @return \Illuminate\Http\Response
     */
    public function get_detail($id)
    {
        $ticket_detail = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $id]);

        if ($ticket_detail == false) {
            return response()->json([
                'error' => "You are losing your connection."
            ]);
        }

        $decode = json_decode($ticket_detail);

        $data['id'] = $id;
        $data['list'] = $decode->data;
        $data['row'] = $decode->data[0];
        $data['id_list'] = $decode->id;

        $result = view("pages.backend.ticket.reference", compact('data'))->render();

        return response()->json([
            'result' => $result
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  get
     * @return \Illuminate\Http\Response
     */
    public function ticket_info($id)
    {
        $user_id = Session::get("ID");

        $ticet_detail = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $id]);
        $destination = $this->call_api_by_parameter("webGetDestination", ["UserID" => $user_id]);
        $customer = $this->call_api_by_parameter("webGetCustomer", ["UserID" => $user_id]);

        if (($ticet_detail == false) || ($destination == false) || ($customer == false)) {
            return response()->json([
                'error' => "You are losing your connection."
            ]);
        }

        $decode = json_decode($ticet_detail);
        $decode_des = json_decode($destination);
        $decode_cus = json_decode($customer);

        if ($decode->id) {

            $data['id'] = $id;
            $data['list'] = $decode->data;
            $data['row'] = $decode->data[0];
            $data['id_list'] = $decode->id;
            $data['date'] = date("d/m/Y h:i A");
            $data['id_des'] = $decode_des->id;
            $data['list_des'] = $decode_des->data;
            $data['id_cus'] = $decode_cus->id;
            $data['list_cus'] = $decode_cus->data;

            $result = view("pages.backend.round.data", compact('data'))->render();

            return response()->json([
                'result' => $result
            ]);
        }
        return response()->json([
            'error' => "No data."
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    //this function to get data by status
    public function get_data(Request $request)
    {
        $user_id = Session::get("ID");

        $ticket_data = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $request->ticket_id]);
        if (($ticket_data == false)) {
            return response()->json([
                'result' => "You are losing your connection."
            ]);
        }

        $decode_data = json_decode($ticket_data);

        if ($decode_data->id) {

            $id_ticket = $decode_data->id;
            $list_ticket = $decode_data->data;

            $status = $request->status;
            $ticket_id = $request->ticket_id;
            $detail_id = $request->detail_id;

            $data['id_data'] = $id_ticket;
            $data['list_data'] = $list_ticket;
            $data['status'] = $status;
            $data['ticket_id'] = $ticket_id;
            $data['detail_id'] = $detail_id;

            $result = view("pages.backend.round.data-status", compact('data'))->render();
            return response()->json([
                'result' => $result
            ]);
        }
        return response()->json([
            'result' => "No data."
        ]);
    }

    public function store(Request $request)
    {

        $driver_id = $request->driver_id;
        $ticket_id = $request->ticket_id_number;
        $detail_id = $request->detail_id;

        $parm_round = array(
            "TicketID" => $ticket_id,
            "TicketDetailID" => $detail_id,
            "UserID" => Session::get("ID"),
            "RoundDateTime" => Carbon::createFromFormat("d/m/Y h:i A", $request->date_time)->format("d-M-Y h:i A"),
            "TruckStatus" => $request->company_status,
            "TruckNo" => $request->truck_number,
            "TrailerNumber" => $request->trailer_number,
            "TrailerStatus" => $request->company_status,
            "Type" => $request->type,
            "TypeName" => $request->type_name,
            "Remark" => $request->remark,
            "DeliveryLocation" => $request->delivery_location,
            "DestinationID" => $request->destination,
            "ContainerNumber1" => $request->container_number1,
            "Feet1" => $request->feet1,
            "ContainerNumber2" => $request->container_number2,
            "Feet2" => $request->feet2,
            "CustomerID1" => $request->customer1,
            "CustomerID2" => $request->customer2,
            "ContainerStatus" => (($request->container_number1 != "") || ($request->container_number2 != "") ? $request->company_status : ""),
        );

        //dd($parm_round);

        $add_round = $this->call_api_by_parameter("webAddRoundTrip", $parm_round);

        //start check credit amount from here
        $find_amount = $this->call_api_by_parameter("webGetAmountRoundTripByID", ["TicketDetailID" => $detail_id, "TypeID" => $request->type]);

        $decode_amount = json_decode($find_amount);
        $amount = 0;

        if ($decode_amount->id) {
            if ($decode_amount->data[0]->Amount) {
                $amount = $decode_amount->data[0]->Amount;
            } else {
                if ($decode_amount->data[0]->RoundTrip == "BV") {
                    $amount = 10;
                } else if ($decode_amount->data[0]->RoundTrip == "PPT") {
                    $amount = 30;
                } else if (($decode_amount->data[0]->RoundTrip == "SHV") && ($request->type == 3)) {
                    $amount = 10;
                }
            }
        }

        if ($amount > 0) {
            $this->call_api_by_parameter("webAddCreditNote", [
                "TicketID" => $ticket_id,
                "AmountFuel" => $amount,
                "UserID" => Session::get("ID"),
                "Remark" => "Port " . $decode_amount->data[0]->RoundTrip . " return as " . $request->type_name
            ]);
        }

        //start update data truck number, trailer number and driver number
        $update_ticket = $this->call_api_by_parameter("webUpdateTicketInfoFromRoundTrip", [
            "TicketID" => $ticket_id,
            "PlateNumber" => $request->truck_number,
            "TrailerNumber" => $request->trailer_number,
            "DriverID" => $driver_id,
            "TicketDetailID" => $detail_id,
            "ContainerNumber1" => $request->container_number1,
            "ContainerNumber2" => $request->container_number2,
        ]);

        if ($add_round || $update_ticket) {
            return response()->json([
                'error' => 0
            ]);
        }
        return response()->json([
            'error' => "You are losing your connection."
        ]);

    }

    public function update(Request $request)
    {

        $parm_round = array(
            "UserID" => Session::get("ID"),
            "RoundDateTime" => Carbon::createFromFormat("d/m/Y h:i A", $request->date_time)->format("d-M-Y h:i A"),
            "TruckStatus" => $request->company_status,
            "TruckNo" => $request->truck_number,
            "TrailerNumber" => $request->trailer_number,
            "TrailerStatus" => $request->company_status,
            "Type" => $request->type,
            "TypeName" => $request->type_name,
            "Remark" => $request->remark,
            "DeliveryLocation" => $request->delivery_location,
            "DestinationID" => $request->destination,
            "ContainerNumber1" => $request->container_number1,
            "Feet1" => $request->feet1,
            "ContainerNumber2" => $request->container_number2,
            "Feet2" => $request->feet2,
            "CustomerID1" => $request->customer1,
            "CustomerID2" => $request->customer2,
            "ContainerStatus" => (($request->container_number1 != "") || ($request->container_number2 != "") ? $request->company_status : ""),
            "RoundTripID" => $request->round_trip_id
        );

        $edit_round = $this->call_api_by_parameter("webEditRoundTrip", $parm_round);

        if ($edit_round) {
            return response()->json([
                'error' => 0
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
    public function get_add(Request $request)
    {
        $user_id = Session::get("ID");
        $status = $request->status;

        $call_customer = $this->call_api_by_parameter("webGetCustomer", ["UserID" => $user_id]);

        if (($call_customer == false)) {
            return response()->json([
                'error' => "You are losing your connection."
            ]);
        }

        $id_customer = 0;
        $list_customer = '';
        $decode_customer = json_decode($call_customer);

        if ($decode_customer->id) {

            $data['id_customer'] = $decode_customer->id;
            $data['list_customer'] = $decode_customer->data;
            $data['status'] = $status;

            $result = view("pages.backend.round.add-data", compact('data'))->render();

            return response()->json([
                'result' => $result
            ]);
        }
        return response()->json([
            'error' => "No data."
        ]);
    }


    //this function for saving data round trip empty
    public function SaveEmpty(Request $request)
    {
        $explod_ticket = explode(',', $request->ticket_num);
        $explod_truck = explode(',', $request->truck_no);
        $explod_round = explode(',', $request->round_trip);
        $explod_trailer = explode(',', $request->trailer_no);
        $explod_detail = explode(',', $request->ticket_detail_id);
        $explod_driver = explode(',', $request->driver_id);

        $i = 0;
        $ticket_id = '';
        $ticket_detail_id = '';
        $truck_no = '';
        $round_trip = '';
        $trailer_no = '';

        foreach ($explod_detail as $item) {

            $ticket_detail_id = $item;
            $ticket_id = $explod_ticket[$i];
            $round_trip = $explod_round[$i];
            $trailer_no = $explod_trailer[$i];
            $truck_no = $explod_truck[$i];
            $driver_id = $explod_driver[$i];

            $parm_round = array(
                "TicketID" => $ticket_id,
                "TicketDetailID" => $ticket_detail_id,
                "UserID" => Session::get("ID"),
                "RoundDateTime" => Carbon::createFromFormat("d/m/Y h:i A", date('d/m/Y h:i A'))->format("d-M-Y h:i A"),
                "TruckStatus" => "Company",
                "TruckNo" => $truck_no,
                "TrailerNumber" => $trailer_no,
                "TrailerStatus" => "Company",
                "Type" => 3,
                "TypeName" => "Only Truck",
                "Remark" => $request->delvery_loca,
                "DeliveryLocation" => $request->round_remark,
                "DestinationID" => '',
                "ContainerNumber1" => '',
                "Feet1" => '',
                "ContainerNumber2" => '',
                "Feet2" => '',
                "CustomerID1" => '',
                "CustomerID2" => '',
                "ContainerStatus" => '',
            );

            $add_round = $this->call_api_by_parameter("webAddRoundTrip", $parm_round);

            //add credit note
            $find_amount = $this->call_api_by_parameter("webGetAmountRoundTripByID", ["TicketDetailID" => $ticket_detail_id, "TypeID" => 3]);

            $decode_amount = json_decode($find_amount);

            if ($decode_amount->id) {

                $this->call_api_by_parameter("webAddCreditNote", [
                    "TicketID" => $ticket_id,
                    "AmountFuel" => $decode_amount->data[0]->Amount,
                    "UserID" => Session::get("ID"),
                    "Remark" => "Port " . $round_trip . " return as Only Truck"
                ]);
            }

            //start update data truck number, trailer number and driver number
            $update_ticket = $this->call_api_by_parameter("webUpdateTicketInfoFromRoundTrip", [
                "TicketID" => $ticket_id,
                "PlateNumber" => $truck_no,
                "TrailerNumber" => $trailer_no,
                "DriverID" => $driver_id,
                "TicketDetailID" => $ticket_detail_id,
                "ContainerNumber1" => '',
                "ContainerNumber2" => '',
            ]);

            $i++;
        }

        return response()->json([
            'error' => 0
        ]);
    }
}
