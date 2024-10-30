<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\Group;
use App\Models\Role;
use AsfyCode\Utils\Request;

class UserController extends Controller{

    public function index(){
        $groups = Group::all();
        $roles = Role::all();
        $factories = Factory::all();
        return view("users.index",compact("groups",'roles','factories'));
    }
    public function totalPrice(){
        return view('users.total_price');
    }
}
