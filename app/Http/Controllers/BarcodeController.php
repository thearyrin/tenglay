<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 5/2/2018
 * Time: 1:57 PM
 */

namespace App\Http\Controllers;


class BarcodeController extends Controller
{
    public function index($number)
    {

        $barcode_font = base_path('public/fonts/ciacode39_m.ttf');

        $number ="*".$number."*";

        $width = 500;
        $height = 180;

        $img = imagecreate($width, $height);

        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);

        imagettftext($img, 36, 0, 20, 160, $black, $barcode_font, $number);

        header('Content-type: image/png');

        imagepng($img);

        imagedestroy($img);
    }
}