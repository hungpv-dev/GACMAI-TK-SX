<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use AsfyCode\Utils\Request;

class DebtController extends Controller{
    public function index(){
        $users = User::all();
        return view('debt.index',compact('users'));
    }
}
