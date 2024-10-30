<?php
namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\LenhXuatMayController;
use App\Models\Order;
use App\Models\Product;
use App\Utils\Export;
use AsfyCode\Utils\Request;
use Exception;

class ExportController extends Controller{

    private $export;

    public function __construct(){
        $this->export = new Export();
    }
    
}
