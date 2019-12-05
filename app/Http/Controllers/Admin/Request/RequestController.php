<?php

namespace App\Http\Controllers\Admin\Request;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use \Milon\Barcode\DNS1D;

class RequestController extends Controller
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
    public function index(Request $request)
    {
        $data_check = $this->check_permission("SYS035", 'view');

        $user_id = session("ID");
        $user_search = $user_id;
        $status_id = 0;
        $from_date = "";
        $to_date = "";
        $start_date = "";
        $end_date = "";
        $fleet_id = -1;
        $fleet_name = "All";
        $driver_id = -1;
        $driver_name = "All";
        $trailer_id = -1;
        $trailer_name = 'All';
        $purpose_id = -1;
        $purpose_name = "All";
        $destination_id = -1;
        $destination_name = "All";
        $request_id = -1;
        $request_number = "All";
        $search = 0;
        $supervisor_name = "All";
        $supervisor_id = -1;
        $reference_id = -1;
        $reference_number = "All";

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $call_user = $this->call_api_by_parameter("webGetUserInRequest", ["UserID" => $user_id]);

        if ($request->isMethod('post')) {

            if ($request->from_date != "") {
                $start_date = Carbon::createFromFormat("d/m/Y", $request->from_date)->format("d-M-Y");
                $from_date = $request->from_date;
            }

            if ($request->to_date != "") {
                $end_date = Carbon::createFromFormat("d/m/Y", $request->to_date)->format("d-M-Y");
                $to_date = $request->to_date;
            }

            $status_id = $request->status;
            $user_search = $request->username;
            $fleet_id = $request->plate_number;
            $fleet_name = $request->fleet_name;
            $driver_id = $request->driver_id;
            $driver_name = $request->driver_name;
            $trailer_id = $request->trailer_number;
            $trailer_name = $request->trailer_name;
            $purpose_id = $request->purpose;
            $purpose_name = $request->purpose_name;
            $destination_id = $request->destination;
            $destination_name = $request->destination_name;
            $request_id = $request->request_id;
            $request_number = $request->request_name;
            $reference_id = $request->reference_number;
            $reference_number = $request->reference_name;
            $supervisor_id = $request->supervisor_id;
            $supervisor_name = $request->supervisor_name;
            $search = 1;

        }

        $parms = [
            "UserID" => $user_search,
            "DriverID" => $driver_id,
            "FleetID" => $fleet_id,
            "TrailerID" => $trailer_id,
            "PurposeID" => $purpose_id,
            "DestinationID" => $destination_id,
            "RequestNumber" => $request_id,
            "Status" => $status_id,
            "FromDate" => $start_date,
            "ToDate" => $end_date,
            "CurrentUserID" => $user_id,
            "SupervisorID" => $supervisor_id,
            "ReferenceNumber" => $reference_id
        ];

        $call_list = $this->call_api_by_parameter("webGetRequestByFilter", $parms);
//        dd($call_list);
        $id_list = 0;
        $data_list = [];
        $id_user = 0;
        $data_user = [];

        if (($call_list) || ($call_user)) {

            $encode_list = json_decode($call_list);
            $encode_user = json_decode($call_user);

            $id_list = $encode_list->id;
            $data_list = $encode_list->data;

            $id_user = $encode_user->id;
            $data_user = $encode_user->data;
        }

        $data['id_list'] = $id_list;
        $data['data_list'] = $data_list;

        $data['id_user'] = $id_user;
        $data['data_user'] = $data_user;

        $data['title'] = "Request List";
        $data['edit'] = CheckPemission($this->check_permission("SYS035", 'edit'));
        $data['ticket'] = CheckPemission($this->check_permission("SYS022", 'add'));

        $data['user_id'] = $user_search;
        $data['status_id'] = $status_id;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['search'] = $search;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;
        $data['driver_id'] = $driver_id;
        $data['driver_name'] = $driver_name;
        $data['trailer_id'] = $trailer_id;
        $data['trailer_name'] = $trailer_name;
        $data['purpose_id'] = $purpose_id;
        $data['purpose_name'] = $purpose_name;
        $data['destination_id'] = $destination_id;
        $data['destination_name'] = $destination_name;
        $data['request_id'] = $request_id;
        $data['request_number'] = $request_number;
        $data['super_name'] = $supervisor_name;
        $data['super_id'] = $supervisor_id;
        $data['ref_name'] = $reference_number;
        $data['ref_id'] = $reference_id;

        session([
            "from_date_request" => $from_date,
            "to_date_request" => $to_date,
            "start_date_request" => $start_date,
            "end_date_request" => $end_date,
            "truck_id_request" => $fleet_id,
            "truck_name_request" => $fleet_name,
            "driver_id_request" => $driver_id,
            "driver_name_request" => $driver_name,
            "user_id_request" => $user_id,
            "trailer_id_request" => $trailer_id,
            "trailer_name_request" => $trailer_name,
            "purpose_id_request" => $purpose_id,
            "purpose_name_request" => $purpose_name,
            "destination_id_request" => $destination_id,
            "destination_name_request" => $destination_name,
            "request_id_request" => $request_id,
            "request_number_request" => $request_number,
            "status_id_request" => $status_id,
            "user_search_request" => $user_search,
            "search_request" => $search,
            "supervisor_id_listreq" => $supervisor_id,
            "supervisor_name_listreq" => $supervisor_name,
            "reference_id_listreq" => $reference_id,
            "reference_name_listreq" => $reference_number,
        ]);

        return view('pages.backend.request.index', compact('data'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data()
    {
        $data_check = $this->check_permission("SYS035", 'view');
        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = session("ID");
        $user_search = session("user_search_request");
        $status_id = session("status_id_request");
        $from_date = session("from_date_request");
        $to_date = session("to_date_request");
        $start_date = session("start_date_request");
        $end_date = session("end_date_request");
        $fleet_id = session("truck_id_request");
        $fleet_name = session("truck_name_request");
        $driver_id = session("driver_id_request");
        $driver_name = session("driver_name_request");
        $trailer_id = session("trailer_id_request");
        $trailer_name = session("trailer_name_request");
        $purpose_id = session("purpose_id_request");
        $purpose_name = session("purpose_name_request");
        $destination_id = session("destination_id_request");
        $destination_name = session("destination_name_request");
        $request_id = session("request_id_request");
        $request_number = session("request_number_request");
        $search = session("search_request");
        $supervisor_id = session("supervisor_id_listreq");
        $supervisor_name = session("supervisor_name_listreq");
        $reference_id = session("reference_id_listreq");
        $reference_number = session("reference_name_listreq");

        $call_user = $this->call_api_by_parameter("webGetUserInRequest", ["UserID" => $user_id]);

        $parms = [
            "UserID" => $user_search,
            "DriverID" => $driver_id,
            "FleetID" => $fleet_id,
            "TrailerID" => $trailer_id,
            "PurposeID" => $purpose_id,
            "DestinationID" => $destination_id,
            "RequestNumber" => $request_id,
            "Status" => $status_id,
            "FromDate" => $start_date,
            "ToDate" => $end_date,
            "CurrentUserID" => $user_id,
            "SupervisorID" => $supervisor_id,
            "ReferenceNumber" => $reference_id
        ];

        $call_list = $this->call_api_by_parameter("webGetRequestByFilter", $parms);

        $id_list = 0;
        $data_list = [];
        $id_user = 0;
        $data_user = [];

        if (($call_list) || ($call_user)) {

            $encode_list = json_decode($call_list);
            $encode_user = json_decode($call_user);

            $id_list = $encode_list->id;
            $data_list = $encode_list->data;

            $id_user = $encode_user->id;
            $data_user = $encode_user->data;
        }

        $data['id_list'] = $id_list;
        $data['data_list'] = $data_list;

        $data['id_user'] = $id_user;
        $data['data_user'] = $data_user;

        $data['title'] = "Request List";
        $data['edit'] = CheckPemission($this->check_permission("SYS035", 'edit'));
        $data['ticket'] = CheckPemission($this->check_permission("SYS022", 'add'));

        $data['user_id'] = $user_search;
        $data['status_id'] = $status_id;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['search'] = $search;
        $data['fleet_id'] = $fleet_id;
        $data['fleet_name'] = $fleet_name;
        $data['driver_id'] = $driver_id;
        $data['driver_name'] = $driver_name;
        $data['trailer_id'] = $trailer_id;
        $data['trailer_name'] = $trailer_name;
        $data['purpose_id'] = $purpose_id;
        $data['purpose_name'] = $purpose_name;
        $data['destination_id'] = $destination_id;
        $data['destination_name'] = $destination_name;
        $data['request_id'] = $request_id;
        $data['request_number'] = $request_number;
        $data['super_name'] = $supervisor_name;
        $data['super_id'] = $supervisor_id;
        $data['ref_name'] = $reference_number;
        $data['ref_id'] = $reference_id;

        return view('pages.backend.request.index', compact('data'));
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

        $driver_id = session("driver_id_request");
        $truck_id = session("truck_id_request");
        $trailer_id = session("trailer_id_request");
        $purpose_id = session("purpose_id_request");
        $destination_id = session("destination_id_request");
        $request_id = session("request_id_request");
        $status_id = session("status_id_request");
        $start_date = session("start_date_request");
        $end_date = session("end_date_request");
        $user_search = session("user_search_request");
        $user_id = session("ID");
        $supervisor_id = session("supervisor_id_listreq");
        $reference_id = session("reference_id_listreq");

        $parms = [
            "UserID" => $user_search,
            "DriverID" => $driver_id,
            "FleetID" => $truck_id,
            "TrailerID" => $trailer_id,
            "PurposeID" => $purpose_id,
            "DestinationID" => $destination_id,
            "RequestNumber" => $request_id,
            "Status" => $status_id,
            "FromDate" => $start_date,
            "ToDate" => $end_date,
            "CurrentUserID" => $user_id,
            "ReferenceNumber" => $reference_id,
            "SupervisorID" => $supervisor_id
        ];

        $call_list = $this->call_api_by_parameter("webGetRequestExportByFilter", $parms);
//        dd($call_list);

        $request = ["No", "Created Date", "Request Number", "User Name", "Reference Number", "Supervisor", "Truck Number", "Team", "Driver ID", "Driver Name",
            "Trailer Number", 'Mix', "Purpose Code", "Purpose", "Destination", "Customer", "Container1", "Feet1", "Container2", "Feet2",
            "Status", "Remark", "Fuel(L)", "Add/Cut(L)", "Total Fuel(L)", "Note", "Date Created Ticket", "Ticket Number"];

        if ($call_list == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        } else {
            $data_json = json_decode($call_list);
            $data = [];

            if ($data_json->id) {

                $data = json_encode($data_json->data);
                $data = json_decode($data, true);
            }

            // and append it to the payments array.
            return Excel::create('Request List Report At ' . $start_date . '-To-' . $end_date, function ($excel) use ($data, $request) {

                $excel->sheet('Request Sheet', function ($sheet) use ($data, $request) {

                    $sheet->fromArray($data, null, 'A1', true);
                    $sheet->row(1, $request);

                    $sheet->getStyle('A1:AB1')->applyFromArray(array(
                        "font" => array(
                            "bold" => true,
                            "color" => array("rgb" => "000000"),
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $data_check = $this->check_permission("SYS035", 'add');

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");

        $date_start = date("d-M-Y");
        $date_end = date("d-M-Y");

        $parms = [
            "UserID" => $user_id,
            "FromDate" => $date_start,
            "ToDate" => $date_end
        ];

        $call_list = $this->call_api_by_parameter("webGetRequestByUser", $parms);

        $id = 0;
        $list = [];

        if ($call_list) {
            $decode_list = json_decode($call_list);
            $id = $decode_list->id;
            $list = $decode_list->data;
        }

        $data['user_id'] = $user_id;
        $data['edit'] = CheckPemission($this->check_permission("SYS035", 'edit'));
        $data['ticket'] = CheckPemission($this->check_permission("SYS022", 'add'));

        $data['id'] = $id;
        $data['list'] = $list;

        return view('pages.backend.request.create', compact('data'));

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
        $group_id = Session::get("group_id");

        $data = array(
            "UserID" => $user_id,
            "FleetID" => $request->truck_number,
            "DriverID" => $request->driver,
            "TrailerID" => $request->trailer,
            "TotalFuel" => $request->total_amount_fuel,
            "ReferenceNumber" => $request->reference_number,
            "SupervisorID" => $request->supervisor
        );

        $add_request = $this->call_api_by_parameter("webAddRequest", $data);

        if ($add_request) {

            $encode_request = json_decode($add_request);

            if ($encode_request->id) {

                $request_id = $encode_request->data[0]->ID;

                $count_destination = count($request->destination);

                $container1_id = 0;
                $container2_id = 0;
                $customer_id = 0;

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
                        $check_container1 = $this->call_api_by_parameter("webGetContainerNumber",
                            [
                                "ContainerNumber" => $request->container_name1[$i],
                                "Feet" => $request->feet1[$i],
                                "UserID" => $user_id,
                                "GroupID" => $group_id
                            ]);

                        $decode_insert1 = json_decode($check_container1);

                        if ($decode_insert1->id) {
                            $container1_id = $decode_insert1->data[0]->ID;
                        }

                        //get container id 2
                        $check_container2 = $this->call_api_by_parameter("webGetContainerNumber",
                            [
                                "ContainerNumber" => $request->container_name2[$i],
                                "Feet" => $request->feet2[$i],
                                "UserID" => $user_id,
                                "GroupID" => $group_id
                            ]);

                        $decode_insert2 = json_decode($check_container2);

                        if ($decode_insert2->id) {
                            $container2_id = $decode_insert2->data[0]->ID;
                        }

                        $parms_detail = [
                            "RequestID" => $request_id,
                            "ReasonID" => $request->reason[$i],
                            "DestinationID" => $request->destination[$i],
                            "Container1ID" => $container1_id,
                            "Container2ID" => $container2_id,
                            "CustomerID" => $customer_id,
                            "AddCut" => $request->add_more[$i],
                            "Fuel" => $request->fuel[$i],
                            "TotalFuel" => $request->total_amount[$i],
                            "Note" => $request->note[$i],
                            "StatusNote" => $status_detail
                        ];

                        $this->call_api_by_parameter("webAddRequestDetail", $parms_detail);

                    }
                }

                $date_start = date("d-M-Y");
                $date_end = date("d-M-Y");

                $parms = [
                    "UserID" => $user_id,
                    "FromDate" => $date_start,
                    "ToDate" => $date_end
                ];

                $request_detail = $this->call_api_by_parameter("webGetRequestByUser", $parms);

                if ($request_detail) {

                    $encode_detail = json_decode($request_detail);
                    if ($encode_detail->id) {
                        return response()->json([
                            'error' => false,
                            'message' => 'Your data was added.',
                            'data' => $encode_detail->data,
                            'id' => $encode_detail->id
                        ]);
                    }
                    return response()->json([
                        'error' => "data no add",
                    ]);

                }

                return response()->json([
                    'error' => "data no add",
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
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $add_request = $this->call_api_by_parameter("webGetRequestDetail", ["ID" => $id]);

        if ($add_request) {

            $decode_request = json_decode($add_request);
            if ($decode_request->id) {
                $data['row'] = $decode_request->data[0];
                $data['list'] = $decode_request->data;
                $data['title'] = 'Request Detail';
                return view('pages.backend.request.detail', compact('data'));
            }
            return redirect('admin/request/list');
        }
        return redirect('admin')->with("Your are losing your connection.");

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['title'] = "Edit Page";
        $user_id = Session::get("ID");

        $call_api_reason = $this->call_api_by_parameter("webGetReason", array("UserID" => $user_id));
        $call_request = $this->call_api_by_parameter("webGetRequestDetail", array("ID" => $id));

        if (($call_api_reason) || ($call_request)) {

            $encode_reason = json_decode($call_api_reason);
            $encode_request = json_decode($call_request);

            if ($encode_request->id) {

                $data['id_reason'] = $encode_reason->id;
                $data['list_reason'] = $encode_reason->data;
                $data['row'] = $encode_request->data[0];
                $data['list'] = $encode_request->data;
                $data['id_request'] = $encode_request->id;
                $data['user_id'] = $user_id;

                return view('pages.backend.request.edit', compact('data'));
            }
            return redirect('admin/request/list');
        }
        return redirect('admin')->with("Your are losing your connection.");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user_id = Session::get("ID");
        $group_id = Session::get("group_id");

        $parm_req = [
            "ID" => $request->request_id,
            "Status" => $request->status,
            "Remark" => $request->remark,
            "UserEditID" => $user_id,
            "TrailerID" => $request->trailer,
            "ReferenceNumber" => $request->reference_number,
            "SupervisorID" => $request->supervisor
        ];

        $update_request = $this->call_api_by_parameter("webUpdateRequest", $parm_req);

        $count_reqd_id = count($request->request_detail_id);

        for ($i = 0; $i < $count_reqd_id; $i++) {

            if ($request->reason[$i] != "") {

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
                $check_container1 = $this->call_api_by_parameter("webGetContainerNumber",
                    [
                        "ContainerNumber" => $request->container_name1[$i],
                        "Feet" => $request->feet1[$i],
                        "UserID" => $user_id,
                        "GroupID" => $group_id
                    ]);

                $decode_insert1 = json_decode($check_container1);

                if ($decode_insert1->id) {
                    $container1_id = $decode_insert1->data[0]->ID;
                }

                //get container id 2
                $check_container2 = $this->call_api_by_parameter("webGetContainerNumber",
                    [
                        "ContainerNumber" => $request->container_name2[$i],
                        "Feet" => $request->feet2[$i],
                        "UserID" => $user_id,
                        "GroupID" => $group_id
                    ]);

                $decode_insert2 = json_decode($check_container2);

                if ($decode_insert2->id) {
                    $container2_id = $decode_insert2->data[0]->ID;
                }

                $parm_reqd = [
                    "RequestDetailID" => $request->request_detail_id[$i],
                    "CustomerID" => $customer_id,
                    "Container1ID" => $container1_id,
                    "Container2ID" => $container2_id,
                    "ReasonID" => $request->reason[$i],
                    "Note" => $request->note[$i]
                ];
                $call_api_update = $this->call_api_by_parameter("webUpdateRequestDetail", $parm_reqd);

            }
        }
        return response()->json([
            'error' => 0,
            'Label' => ''
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request)
    {
        $user_id = Session::get("ID");
        $parms = [
            "ID" => $request->id,
            "Status" => $request->status,
            "Remark" => $request->remark,
            "UserID" => $user_id
        ];

        $call_api = $this->call_api_by_parameter("webUpdateRequestStatus", $parms);
        if ($call_api) {
            $encode_api = json_decode($call_api);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function GetFleet(Request $request)
    {
		$user_id = session("ID");
		
        $term = trim($request->q);

       // $data_json = $this->call_api_by_parameter("webGetFleetByNumber", ['PlateNumber' => $term]);
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


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $FleetID
     * @return \Illuminate\Http\Response
     */
    public function GetTeamByFleetID($FleetID)
    {

        $call_api_driver = $this->call_api_by_parameter("webGetFleetDriver", ["FleetID" => $FleetID]);
        $call_team = $this->call_api_by_parameter("webGetFleetTeam", ["FleetID" => $FleetID]);

        if (($call_api_driver) && ($call_team)) {

            $deconde_json = json_decode($call_api_driver);
            $decode_team = json_decode($call_team);

            $team_id = 0;
            $fuel_add = 0;
            $team_name = '';

            if ($decode_team->id) {
                $team_id = $decode_team->id;
                $fuel_add = $decode_team->data[0]->FuelAdd;
                $team_name = $decode_team->data[0]->Team;
            }

            return response()->json([
                'driver_id' => $deconde_json->id,
                'driver_list' => $deconde_json->data,
                'team_id' => $team_id,
                'fuel_add' => $fuel_add,
                'team_name' => $team_name
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function GetTrailer(Request $request)
    {
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetTrailerByNumber", ['TrailerNumber' => $term]);

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


    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function GetCustomer(Request $request)
    {
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetCustomerByName", ['CustomerName' => $term]);

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


    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function GetContainer(Request $request)
    {
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetContainerFilterByNumber", ['ContainerNumber' => $term]);

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function CloneData(Request $request)
    {
        $data['id'] = $request->id;

        $result = view("pages.backend.request.clone", compact('data'))->render();

        return response()->json([
            'result' => $result
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function Autocomplete(Request $request)
    {
        $term = trim($request->value);
        $data_json = $this->call_api_by_parameter("webGetNoteRequestHistory", ['Value' => $term]);

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
                    $output .= '<li class="text_note" data-number="' . $request->number . '">' . $item->Note . '</li>';
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

    //this function for creating ticket
    public function create_ticket(Request $request)
    {
        $user_id = Session::get("ID");
        $id = $request->request_all_id;
        $count_id = count($id);

//        dd($request->all());

        if ($count_id > 0) {

            $diesel_return_note = "";
            $diesel_return_amount = 0;
            $request_id = $request->request_all_id;
            $count_credit_id = count($request->credit_all_id);
            $credit_id = $request->credit_all_id;

            if ($count_credit_id > 0) {
                $get_credit = $this->call_api_by_parameter("webGetCreditNoteFilterByID", ["UserID" => $user_id, "CreditID" => $credit_id]);
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
                            $diesel_return_note .= $data_credit->Remark . $coma;
                            $diesel_return_amount += $data_credit->Amount;
                        }
                    }
                }

            }


            $parm = [
                "ID" => $request_id,
                "UserID" => $user_id,
                "diesel_return_amount" => ($diesel_return_amount > 0 ? ((-1) * $diesel_return_amount) : ""),
                "diesel_return_note" => $diesel_return_note,
                "ticket_amount_fuel" => ($diesel_return_amount > 0 ? (($request->original_fuel) + ((-1) * $diesel_return_amount)) : $request->original_fuel),
            ];

            $call_api_request = $this->call_api_by_parameter("webGetRequestAddTicket", $parm);

            if ($call_api_request) {

                $decode_request = json_decode($call_api_request);

                if ($decode_request->id) {
                    $ticket_id = $decode_request->data[0]->TicketID;

                    $get_ticket_de = $this->call_api_by_parameter("webGetTicketDetail", ["TicketID" => $ticket_id]);

                    if ($count_credit_id > 0) {
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

    //this function for getting fleet in request
    public function get_fleet_in_request(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);
        $data_json = $this->call_api_by_parameter("webGetFleetInRequest", ['PlateNumber' => $term, "UserID" => $user_id]);

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

    //this function for getting fleet in request
    public function get_driver_in_request(Request $request)
    {

        $user_id = Session::get("ID");
        $term = trim($request->q);
        $data_json = $this->call_api_by_parameter("webGetDriverInRequest", ['DriverName' => $term, "UserID" => $user_id]);

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

    //this function for getting fleet in request
    public function get_trailer_in_request(Request $request)
    {

        $user_id = Session::get("ID");

        $term = trim($request->q);
        $data_json = $this->call_api_by_parameter("webGetTrailerInRequest", ['TrailerNumber' => $term, "UserID" => $user_id]);

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

    //this function for getting fleet in request
    public function get_reason_in_request(Request $request)
    {
        $user_id = Session::get("ID");

        $term = trim($request->q);
        $data_json = $this->call_api_by_parameter("webGetReasonInRequest", ['Purpose' => $term, "UserID" => $user_id]);

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

    //this function for getting fleet in request
    public function get_purpose_in_request(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);
        $data_json = $this->call_api_by_parameter("webGetDestinationInRequest", ['Destination' => $term, "UserID" => $user_id]);

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

    //this function for getting fleet in request
    public function get_request_number(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);
        $data_json = $this->call_api_by_parameter("webGetRequestNumber", ['RequestNumber' => $term, "UserID" => $user_id]);
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

    //this function for getting get_reference_number in request
    public function get_reference_number(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);
        $data_json = $this->call_api_by_parameter("webGetReferenceNumber", ['ReferenceNumber' => $term, "UserID" => $user_id]);
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

    //this function for getting get_supervisor_id in request
    public function get_supervisor_id(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);
        $data_json = $this->call_api_by_parameter("webGetSupervisorFilter", ['Supervisor' => $term, "UserID" => $user_id]);
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

    //this function for getting supervisor in request
    public function get_supervisor(Request $request)
    {
        $user_id = Session::get("ID");
        $term = trim($request->q);

        $data_json = $this->call_api_by_parameter("webGetSupervisorByFilter", ['NextName' => $term, "UserID" => $user_id]);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $user_id = Session::get("ID");

        $data_api = $this->call_api_by_parameter("webDeleteRequest", array("UserID" => $user_id, "ID" => $request->id));

        if ($data_api) {
            $decode = json_decode($data_api);
            if ($decode->id) {
                return response()->json([
                    "message" => "Data Request Deleted",
                    "error" => false
                ]);
            }

            return response()->json([
                'error' => $decode->message
            ]);
        }

        return response()->json([
            'error' => "You are losing your connection"
        ]);
    }

}
