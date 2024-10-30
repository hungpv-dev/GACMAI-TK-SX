<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use AsfyCode\Utils\Request;

class SaleChannelController extends Controller{
    public function index(){
        return view("customers.sale_channel.index");
    }
}
