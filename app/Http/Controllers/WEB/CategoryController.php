<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use AsfyCode\Utils\Request;

class CategoryController extends Controller{
    public function index(){
        $units = Unit::all();
        return view('categories.index',compact('units'));
    }
}
