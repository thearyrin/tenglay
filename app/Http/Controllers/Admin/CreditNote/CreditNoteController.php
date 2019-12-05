<?php

namespace App\Http\Controllers\Admin\CreditNote;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use \Milon\Barcode\DNS1D;
use Carbon\Carbon;

class CreditNoteController extends Controller
{
    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validation_create_ticket = [
        'ticket_number' => 'required',
        'amount_fuel' => 'required',
        'remark' => 'required',
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $data_check = $this->check_permission("SYS023", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = session("ID");

        $request->session()->forget([
            "date_start_credit",
            "date_end_credit",
            "ticket_id_credit",
            "ticket_number_credit",
            "credit_id_credit",
            "credit_number_credit",
            "status_id_credit",
            "from_date_credit",
            "to_date_credit",
            "search_credit",
            "status_credit",
        ]);

        $date_start = "";
        $date_end = "";
        $from_date = "";
        $to_date = "";
        $ticket_id = -1;
        $ticket_number = "All";
        $status_id = 1;
        $credit_id = -1;
        $credit_number = "All";
        $search = 0;

        if ($request->isMethod('post')) {

            $ticket_id = $request->ticket_id;
            $ticket_number = $request->ticket_number;
            $status_id = $request->status;
            $credit_id = $request->credit_id;
            $credit_number = $request->credit_number;
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
            "FromDate" => $date_start,
            "ToDate" => $date_end,
            "TicketID" => $ticket_id,
            "Status" => $status_id,
            "CreditNo" => $credit_id,
        ];

        $call_credit = $this->call_api_by_parameter("webGetCreditNote", $parms);

        if ($call_credit == false) {
            return redirect('/admin')->withErrors("You are losing your connection");
        }

        $decode_credit = json_decode($call_credit);

        $data['user_id'] = $user_id;
        $data['search'] = $search;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['status'] = $status_id;
        $data['credit_id'] = $credit_id;
        $data['credit_number'] = $credit_number;
        $data['id_credit'] = $decode_credit->id;
        $data['list_credit'] = $decode_credit->data;

        session([
            "date_start_credit" => $from_date,
            "date_end_credit" => $to_date,
            "ticket_id_credit" => $ticket_id,
            "ticket_number_credit" => $ticket_number,
            "credit_id_credit" => $credit_id,
            "credit_number_credit" => $credit_number,
            "status_id_credit" => $status_id,
            "from_date_credit" => $date_start,
            "to_date_credit" => $date_end,
            "search_credit" => $search,
        ]);

        return view('pages.backend.credit.show', compact('data'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data()
    {

        $data_check = $this->check_permission("SYS023", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = session("ID");

        $date_start = session("date_start_credit");
        $date_end = session("date_end_credit");
        $ticket_id = session("ticket_id_credit");
        $credit_id = session("credit_id_credit");
        $status_id = session("status_id_credit");
        $from_date = session("from_date_credit");
        $to_date = session("to_date_credit");
        $search = session("search_credit");
        $ticket_number = session("ticket_number_credit");
        $credit_number = session("credit_number_credit");

        $parms = [
            "FromDate" => $from_date,
            "ToDate" => $to_date,
            "TicketID" => $ticket_id,
            "Status" => $status_id,
            "CreditNo" => $credit_id,
        ];

        $call_credit = $this->call_api_by_parameter("webGetCreditNote", $parms);

        if ($call_credit == false) {
            return redirect('/admin')->withErrors("You are losing your connection");
        }

        $decode_credit = json_decode($call_credit);
        $data['user_id'] = $user_id;
        $data['search'] = $search;
        $data['from_date'] = $date_start;
        $data['to_date'] = $date_end;
        $data['ticket_id'] = $ticket_id;
        $data['ticket_number'] = $ticket_number;
        $data['status'] = $status_id;
        $data['credit_id'] = $credit_id;
        $data['credit_number'] = $credit_number;
        $data['id_credit'] = $decode_credit->id;
        $data['list_credit'] = $decode_credit->data;

        return view('pages.backend.credit.show', compact('data'));
    }

    /**
     * Export data to excel
     */
    public function export()
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $from_date = Session::get("date_start_credit");
        $to_date = Session::get("date_end_credit");
        $ticket_id = Session::get("ticket_id_credit");
        $credit_id = Session::get("credit_id_credit");
        $status_id = Session::get("status_id_credit");
        $status = Session::get("status_credit");

        $parms = [
            "FromDate" => $from_date,
            "ToDate" => $to_date,
            "TicketID" => $ticket_id,
            "Status" => $status,
            "CreditNo" => $credit_id,
        ];

        $call_credit = $this->call_api_by_parameter("webGetCreditNote", $parms);

        $credit = array();
        $credit[] = ['No', 'Created Date', 'Diesel Return Number', 'Reference Number', 'Plate Number', 'Driver ID', 'Driver Name', 'Ticket Number Used', 'Amount', 'Remark', 'Status'];
        if ($call_credit == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        } else {
            $data_json = json_decode($call_credit);
            $i = 1;

            if ($data_json->id) {
                foreach ($data_json->data as $row) {
                    if ($row->Status == 0) {
                        $status = "Used";
                    } elseif ($row->Status == 1) {
                        $status = "No Use";
                    } elseif ($row->Status == 2) {
                        $status = "Cancel";
                    } elseif ($row->Status == 3) {
                        $status = "Pending";
                    }

                    $credit[] = array($row->ID, $row->CreatedDate, $row->CreditNo, $row->TicketNo, $row->PlateNumber, $row->CodeID, $row->NameKh, $row->TickeUsedNo, $row->Amount . " L", $row->Remark, $status);
                    $i++;
                }
            }

            // and append it to the payments array.
            return \Excel::create('Diesel Return List Report At ' . $from_date . '-To-' . $to_date, function ($excel) use ($credit, $i) {
                $excel->sheet('sheet name', function ($sheet) use ($credit, $i) {

                    $sheet->cells('A1:K1', function ($cells) {
                        $cells->setAlignment('center');
                        $cells->setFontWeight("bold");
                        $cells->setFontColor('#000000');
                    });

                    $sheet->fromArray($credit, null, 'A1', false, false);
                });
            })->download('xlsx');
        }
    }

    //this function for reprinting ticket
    public function RePrint(Request $request)
    {

        $call_api = $this->call_api_by_parameter("webGetCreditNoteByID", ["TicketID" => $request->ticket_id]);
        $get_ticket_de = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $request->ticket_id]);

        if ($get_ticket_de) {

            $encode_detail = json_decode($get_ticket_de);
            $decode_api = json_decode($call_api);

            if ($encode_detail->id) {

                $data['credit_data'] = $decode_api->data;
                $data['credit_id'] = $decode_api->id;

                $data['data'] = $encode_detail->data;

                $label = view('pages.backend.credit.print', compact('data'))->render();

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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $data_check = $this->check_permission("SYS023", 'add');

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $data['user_id'] = $user_id;

        return view('pages.backend.credit.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user_id = Session::get("ID");

//        $validation = Validator::make($request->all(), $this->validation_create_ticket);
//
//        if ($validation->fails()) {
//            return response()->json([
//                'error' => $validation->errors()->first()
//            ]);
//        }

        $status = 1;
        if ($request->amount_fuel_credit <= 0) {
            $status = 0;
        }

        $parms = [
            "TicketID" => $request->ticket_number,
            "AmountFuel" => $request->amount_fuel_credit,
            "PayTripAmount" => $request->paytrip_amount_credit,
            "LoloAmount" => $request->lolo_amount_credit,
            "Remark" => $request->remark_credit,
            "UserID" => $user_id,
            "Status" => $status
        ];

        $call_api = $this->call_api_by_parameter("webAddCreditNote", $parms);

        if ($call_api) {

            $decode_api = json_decode($call_api);

            if ($decode_api->id) {

                $get_ticket_de = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $request->ticket_number]);

                if ($get_ticket_de) {

                    $encode_detail = json_decode($get_ticket_de);

                    if ($encode_detail->id) {

                        $data['credit_data'] = $decode_api->data;
                        $data['credit_id'] = $decode_api->id;

                        $data['data'] = $encode_detail->data;

                        $label = view('pages.backend.credit.print', compact('data'))->render();

                        return response()->json([
                            'error' => 0,
                            'Label' => $label
                        ]);
                    }

                    return response()->json([
                        'error' => "something wrong with your data1."
                    ]);
                }

            }


            return response()->json([
                'error' => 'something wrong with your data2.'
            ]);
        }
        return response()->json([
            'error' => 'You are losing your connection. Please check your connection'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  post
     * @return \Illuminate\Http\Response
     */
    public function get_reference_info(Request $request)
    {
        $ticet_detail = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $request->ticket_id]);

        if ($ticet_detail == false) {
            return response()->json([
                'error' => "You are losing your connection."
            ]);
        }

        $decode = json_decode($ticet_detail);

        $data['list'] = $decode->data;
        $data['row'] = $decode->data[0];
        $data['id_list'] = $decode->id;

        $result = view("pages.backend.credit.reference_info", compact('data'))->render();

        return response()->json([
            'result' => $result,
            'volume' => $decode->data[0]->TotalAmountFuel
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  get
     * @return \Illuminate\Http\Response
     */
    public function get_reference($id)
    {
        $ticket_detail = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $id]);

        if ($ticket_detail == false) {
            return response()->json([
                'error' => "You are losing your connection."
            ]);
        }

        $decode = json_decode($ticket_detail);

        $data['list'] = $decode->data;
        $data['row'] = $decode->data[0];

        $result = view("pages.backend.credit.reference", compact('data'))->render();

        return response()->json([
            'result' => $result
        ]);
    }

    /**
     * Show the data for testing print credit note
     *
     */
    function testing()
    {

        $ticket_id = 10;
        $get_ticket_de = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $ticket_id]);

        if ($get_ticket_de) {

            $encode_detail = json_decode($get_ticket_de);

            if ($encode_detail->id) {

                $data['data'] = $encode_detail->data;

                return view('pages.backend.credit.testing', compact('data'));
            }

            return redirect('/admin')->withErrors("something wrong with your data");
        }

        return redirect('/admin')->withErrors("You are losing your connection. Please check your connection");
    }

    //get_ticket_number
    public function get_ticket_number(Request $request)
    {
        $term = trim($request->q);
        $user_id = session("ID");

        $data_json = $this->call_api_by_parameter("webGetTicketUsedNoCredit", ['TicketNumber' => $term, "UserID" => $user_id]);
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
