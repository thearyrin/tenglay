<?php

namespace App\Http\Controllers\Admin\Rescan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use \Milon\Barcode\DNS1D;

class RescanController extends Controller
{

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';


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
    public function index()
    {
        $user_id = Session::get("ID");

        $data_check = $this->check_permission("SYS034", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $call_api = $this->call_api_by_parameter("webGetTicketRemainder", ["UserID" => $user_id]);

        if (($call_api == false)) {
            return redirect('/admin')->withErrors("You are losing your connection");
        }

        $decode_api = json_decode($call_api);

        $data['title'] = "Rescan Ticket";
        $data['list'] = $decode_api->data;
        $data['id'] = $decode_api->id;
        $data['authorize_per'] = CheckPemission($this->check_permission("SYS034", "add"));
        $data['delete'] = CheckPemission($this->check_permission("SYS034", "delete"));

        return view("pages.backend.rescan.index", compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $user_id = Session::get("ID");

        $call_api = $this->call_api_by_parameter("webGetTicketRemainder", ["UserID" => $user_id]);

        $list = array();
        $list[] = ['No', 'Transaction #', 'Sale #', 'DateTime Start', 'DateTime End', 'Truck #', 'Driver', 'Ticket #', 'Preset(L)', 'Actual(L)', 'Remainder(L)'];

        if ($call_api == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");

        } else {
            $data_json = json_decode($call_api);
            $i = 1;

            if ($data_json->id) {
                foreach ($data_json->data as $row) {
                    $list[] = array($i, $row->TransactionNumber, $row->SaleNumber, $row->StartDate, $row->FinishDate, $row->PlateNumber,
                        $row->DriverName, $row->TicketNumber, $row->PresetValue, $row->ActualValue, $row->Remainder);
                    $i++;
                }
            }

            // and append it to the payments array.
            return \Excel::create('Rescan List Report', function ($excel) use ($list, $i) {
                $excel->sheet('sheet name', function ($sheet) use ($list, $i) {

                    $sheet->getStyle('A1:K1')->applyFromArray(array(
                        "font" => array(
                            "bold" => true,
                            "color" => array("rgb" => "000000"),
                        ),

                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
                        ),
                    ));

                    $sheet->fromArray($list, null, 'A1', false, false);

                });
            })->download('xlsx');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tsl_id = $request->tsl_id;
        $sale_id = $request->sale_id;
        $tsl_number = $request->tsl_number;
        $ticket_id = $request->tsl_number;
        $remainder = $request->remainder;
        $fleet_id = $request->fleet_id;
        $driver_id = $request->driver_id;
        $ticket_number = $request->ticket_number;
        $user_id = Session::get("ID");

        $data = array(
            "TSLID" => $tsl_id,
            "SaleID" => $sale_id,
            "UserID" => $user_id,
            "Remainder" => $remainder,
            "Ticket_ID" => $ticket_id,
            "FleetID" => $fleet_id,
            "DriverID" => $driver_id,
            "Ticket_Number" => $ticket_number,
        );

        $call_ticket = $this->call_api_by_parameter("webAddTicketRemainder", $data);

        if ($call_ticket) {

            $encode_ticket = json_decode($call_ticket);

            if ($encode_ticket->id) {

                $ticket_last_id = $encode_ticket->data[0]->TicketID;

                $get_ticket_de = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $ticket_last_id]);

                if ($get_ticket_de) {

                    $encode_detail = json_decode($get_ticket_de);

                    if ($encode_detail->id) {

                        $data['data'] = $encode_detail->data;
                        $data['barcode'] = $encode_detail->data[0]->Barcode;
                        $data['img'] = DNS1D::getBarcodePNG($encode_detail->data[0]->Barcode, 'C39');

                        $label = view('pages.backend.ticket.print', compact('data'))->render();

                        return response()->json([
                            'error' => 0,
                            'Label' => $label,
                            'message' => 'success'
                        ]);
                    }

                    return response()->json([
                        'error' => "something wrong with your data."
                    ]);
                }

                return response()->json([
                    'error' => 'You are losing your connection. Please check your connection.'
                ]);
            }
            return response()->json([
                'error' => "something wrong with your data."
            ]);
        }

        return response()->json([
            'error' => 'You are losing your connection. Please check your connection.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $user_id = Session::get("ID");
        $id = $request->id;
        $count_id = count($id);
        $sale_id = '';
        if ($count_id > 0) {
            $i = 0;
            foreach ($id as $ids) {

                $i++;

                $coma = ",";
                if ($count_id == $i) {
                    $coma = '';
                }

                $sale_id .= $ids . $coma;
            }

            $parm = [
                "SaleID" => $sale_id,
                "UserID" => $user_id,
            ];

            $call_rescan = $this->call_api_by_parameter("webClearRescanTicket", $parm);
            if ($call_rescan) {

                $decode_rescan = json_decode($call_rescan);
                if ($decode_rescan->id) {
                    return response()->json([
                        'error' => 1,
                        "message" => "Clear success"
                    ]);
                }

                return response()->json([
                    'error' => "No data."
                ]);
            }

            return response()->json([
                'error' => "No connection"
            ]);
        }

        return response()->json([
            'error' => "No data."
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
