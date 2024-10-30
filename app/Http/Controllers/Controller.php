<?php 
namespace App\Http\Controllers;


class Controller
{
    public function sendResponse($data,$status = 200) {
        return response()->json($data,$status);
    }
}

