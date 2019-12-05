<?php

namespace App\Http\Controllers\Admin\Setting\Supervisor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SupervisorController extends Controller
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

        $data_check = $this->check_permission("SYS037", "view");

        if ($data_check == false) {
            return redirect('/admin')->withErrors("You are losing your connection.");
        }

        $check_menu = CheckPemission($data_check);

        if ($check_menu == false) {
            return view('pages.backend.message');
        }

        $user_id = Session::get("ID");
        $data_json = $this->call_api_by_parameter("webGetSupervisor", ['UserID' => $user_id]);
        if ($data_json == false) {
            return redirect('/admin')->withErrors("Your are losing your connection");
        }

        $data_encode = json_decode($data_json);

        $data['id'] = $data_encode->id;
        $data['list'] = $data_encode->data;
        $data['user_id'] = $user_id;

        $data['add'] = CheckPemission($this->check_permission("SYS037", 'add'));
        $data['edit'] = CheckPemission($this->check_permission("SYS037", 'edit'));
        $data['delete'] = CheckPemission($this->check_permission("SYS037", 'delete'));

        return view('pages.backend.setting.supervisor.show', compact("data"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {

        $date_in = '';
        if (!empty($request->date_in)) {
            $date_in = Carbon::createFromFormat("d/m/Y", $request->date_in)->format("Y-m-d");
        }

        if ($request->id) {

            $name = "webEditSupervisor";

            $parmsadd = [
                "ID" => $request->id,
                "UserID" => $request->user_id,
                "CodeID" => $request->supervisor_number,
                "NameKh" => $request->khmer_name,
                "NameLatin" => $request->latin_name,
                "Gender" => $request->gender,
                "DateIn" => $date_in,
                "Position" => $request->position,
                "NextName" => $request->nextname,
                "Status" => $request->status,
                "Department" => $request->department,
            ];
        } else {
            $parmsadd = [
                "UserID" => $request->user_id,
                "CodeID" => $request->supervisor_number,
                "NameKh" => $request->khmer_name,
                "NameLatin" => $request->latin_name,
                "Gender" => $request->gender,
                "DateIn" => $date_in,
                "Position" => $request->position,
                "NextName" => $request->nextname,
                "Department" => $request->department,
            ];

            $name = "webAddSupervisor";
        }

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
    public function delete(Request $request)
    {
        $data_api = $this->call_api_by_parameter("webDeleteSupervisor", array("UserID" => $request->user_id, "ID" => $request->id));

        if ($data_api) {
            $decode = json_decode($data_api);
            if ($decode->id) {
                return response()->json([
                    "message" => "Data Supervisor Deleted"
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

    protected function validator(array $data)
    {
        //$data would be an associative array like ['date_value' => '15.15.2015']
        $message = [
            'date_in.date' => 'invalid date, enduser understands the error message'
        ];
        return Validator::make($data, [
            'date_in' => 'date',
        ], $message);
    }


    //this function for import data fleet
    public function import(Request $request)
    {

        set_time_limit(1000);

        $user_id = Session::get("ID");

        $validator = Validator::make(
            [
                'file' => $request->file_excel,
                'extension' => strtolower($request->file_excel->getClientOriginalExtension()),
            ],
            [
                'file' => 'required',
                'extension' => 'required|in:xlsx,xls',
            ]
        );
        // process the form
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ]);
        }

        try {

            $i = 0;
            $j = 0;
            $k = 0;

            $reader = \Excel::load($request->file_excel)->toArray();

            foreach ($reader as $row) {
                $date_in = $row['date_in'];

                if (!empty($date_in)) {
                    $date_in = Carbon::parse($date_in)->format('Y-m-d');
                }

                $parmsadd = [
                    "UserID" => $user_id,
                    "CodeID" => $row['code_id'],
                    "NameKh" => $row['name_kh'],
                    "NameLatin" => $row['name_latin'],
                    "Gender" => $row['gender'],
                    "DateIn" => $date_in,
                    "Position" => $row['position'],
                    "NextName" => $row['next_name'],
                    "Department" => $row['department'],
                ];

                $call_api = $this->call_api_by_parameter("webAddSupervisor", $parmsadd);
                $decode = json_decode($call_api);

                if ($decode->id) {
                    $j++;
                } else {
                    $k++;
                }

                $i++;
            }

            return response()->json([
                'message' => "Your file upload completed. Total data is " . $i . " rows. Inserted: " . $j . " rows, Not insert: " . $k . " rows"
            ]);


        } catch
        (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    //this function for download example purpose excel
    public function download()
    {

        $cus_list = array();
        $cus_list[] = ['Code ID', 'Name Kh', 'Name Latin', 'Next Name', 'Gender', 'Date In', 'Position', 'Department'];
        $cus_list[] = ['001', 'Supervisor1', 'Supervisor1', 'TL1', 'Male', '27/09/2019', 'Supervisor', 'Trucking in TLD'];
        $cus_list[] = ['002', 'Supervisor2', 'Supervisor2', 'TL2', 'Male', '27/09/2019', 'Supervisor', 'Trucking in TLD'];
        $cus_list[] = ['003', 'Supervisor3', 'Supervisor3', 'TL3', 'Male', '27/09/2019', 'Supervisor', 'Trucking in TLD'];

        return \Excel::create('supervisor_example', function ($excel) use ($cus_list) {

            $excel->sheet('sheet name', function ($sheet) use ($cus_list) {

                $sheet->fromArray($cus_list, null, 'A1', false, false);
                $sheet->cells('A1:H1', function ($cells) {

                    $cells->setAlignment('center');
                    $cells->setFontWeight("bold");
                    $cells->setFontColor('#000000');
                    $cells->setFontSize('12');
                });
            });
        })->download('xlsx');
    }
}
