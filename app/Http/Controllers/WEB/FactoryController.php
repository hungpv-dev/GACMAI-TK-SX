<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use AsfyCode\Utils\Request;

class FactoryController extends Controller{
    public function index(){
        return view("factories.index");
    }
}