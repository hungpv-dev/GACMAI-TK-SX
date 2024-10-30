<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use AsfyCode\Utils\Request;

class VNController extends Controller{
    public function provinces(){
        return $this->sendResponse(Province::all());
    }
    public function districts($province_id){
        return $this->sendResponse(District::where('province_id',$province_id)->get());
    }
    public function wards($district_id){
        return $this->sendResponse(Ward::where('district_id',$district_id)->get());
    }
}