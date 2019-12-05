<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Session;
use File;

class Controller extends BaseController
{
	protected $config ="http://localhost:82/api/webapi/"; 
	protected $username = "sduser";
	protected $password = "sduser";
	//protected $username = "tenglaygroup\sd.work";
    //protected $password = "D!mgms@t8";


    protected $api_check = "webCheckUserProcess";

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //This function for call API URL and Return data back
    function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'APIKEY: 111111111111111111111',
            'Content-Type: application/json',
        ));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "$this->username:$this->password");

        $result = curl_exec($curl);
//        dd($result);

        // Check HTTP status code
        if (!curl_errno($curl)) {
            switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
                case 200:
                    $result = $result;
                    break;
                default:
                    $result = null;
                    break;
            }
        }

        curl_close($curl);

        return $result;

    }

    //this function for call all api via get parameter from client
    public function call_api_by_parameter($api_name, $param = [])
    {

        $string = '[{';
        $count = count($param);
        $i = 1;

        foreach ($param as $key => $value) {

            if ($count == $i) {
                $commar = '';
            } else {
                $commar = ',';
            }

            if ($value == null) {
                $string .= '"' . $key . '":"' . ($value) . '"' . $commar;
            } else {
                $string .= '"' . $key . '":"' . urlencode($value) . '"' . $commar;
            }

            $i++;
        }

        $string .= '}]';

        $url = $this->config . $api_name . "?strEJSON=" . $string;
//dd($url);

        $data_api = $this->CallAPI("GET", $url);

        if ($data_api == null) {

            Session::forget("is_logged");
            Session::forget("ID");
            Session::forget("UserName");
            Session::forget("PhoneNumber");
            Session::forget("DisplayName");
            Session::forget("photo");
            Session::forget("password");

            return null;

        } else {
            return $data_api;
        }
    }

    //this function for call all api via get parameter post from client
    public function call_api_by_parameter_post($api_name, $data, $filenames)
    {

        $curl = curl_init();

        $url = $this->config . $api_name;

        $files = array();
        foreach ($filenames as $f) {
            if ($f != "") {
                $files["PhotoBase64"] = file_get_contents($f);
            }
        }

        $url_data = http_build_query($data);

        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;

        $post_data = $this->build_data_files($boundary, $data, $files);


        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERPWD => "$this->username:$this->password",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                //"Authorization: Bearer $TOKEN",
                "Content-Type: multipart/form-data; boundary=" . $delimiter,
                "Content-Length: " . strlen($post_data)

            ),


        ));

        $response = curl_exec($curl);
//        dd($response);

        if (!curl_errno($curl)) {
            switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
                case 200:
                    $response = $response;
                    break;
                default:
                    $response = null;
                    break;
            }
        }

        curl_close($curl);

        return $response;
    }

    //this function to build file
    function build_data_files($boundary, $fields, $files)
    {
        $data = '';
        $eol = "\r\n";

        $delimiter = '-------------' . $boundary;

        foreach ($fields as $name => $content) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $name . "\"" . $eol . $eol
                . $content . $eol;
        }


        foreach ($files as $name => $content) {

            if ($content != "") {
                $data .= "--" . $delimiter . $eol
                    . 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $name . '"' . $eol
                    //. 'Content-Type: image/png'.$eol
                    . 'Content-Transfer-Encoding: binary' . $eol;

                $data .= $eol;
                $data .= $content . $eol;
            }
        }

        $data .= "--" . $delimiter . "--" . $eol;

        return $data;
    }

    //this function for checking data login
    function check_permission($menu_code, $action = '')
    {

        $user_id = Session::get("ID");

        $parm = [
            "UserCheck" => $user_id,
            "UserID" => $user_id,
            "ProcessCode" => $menu_code,
            "Action" => $action
        ];

//        dd($parm);

        $call = $this->call_api_by_parameter($this->api_check, $parm);
//        dd($call);

        return $call;
    }

    //this function for checking data login frontend
    //this function for checking data login
    function check_permission_front($menu_code, $action = '')
    {

        $user_id = Session::get("user_id");

        $parm = [
            "UserCheck" => $user_id,
            "UserID" => $user_id,
            "ProcessCode" => $menu_code,
            "Action" => $action
        ];

//        dd($parm);

        $call = $this->call_api_by_parameter($this->api_check, $parm);
//        dd($call);

        return $call;
    }


}
