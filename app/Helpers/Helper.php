<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 6/8/2018
 * Time: 11:14 AM
 */
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

function CheckPemission($data=[]){

    $decode = json_decode($data);
    if($decode->id){
        return true;
    }

    return false;
}