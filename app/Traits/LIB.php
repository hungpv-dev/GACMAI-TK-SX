<?php 
namespace App\Traits;

use AsfyCode\Facades\DB;

trait LIB
{
    public static function getChiPhiByFactory(int $factory_id, $status_product) {
        // không tính chi phí nhân công
        // 1. 10/nvl + 1.56/bang_gia + 10.83/bang_gia
        // 2. 10/nvl + 8/bang_gia + 9.83/thuc_thu
        // 3. 10,8/bang_gia - may_sua_chua=1 || 12,8/bang_gia - may_sua_chua=2 + 10.83/bang_gia
        $rs = [];
            
        if ($factory_id == 1) {
            $item = DB::table('products')
                    ->selectRaw('COUNT(*)AS total_count, SUM(gia_sx) AS total_gia_sx, SUM(gia_niem_yet) AS total_bang_gia, SUM(thuc_thu_mbt) AS total_thuc_thu ')
                    ->whereRaw("factory_id=$factory_id and $status_product")->first();
            $rs['total_count'] = $item->total_count;
            $rs['chi_phi'] = $item->total_gia_sx + $item->total_gia_sx*10/100 + $item->total_bang_gia*(1.56 + 10.83)/100;
        }
        if ($factory_id == 2) {
            $item = DB::table('products')
                    ->selectRaw('COUNT(*)AS total_count, SUM(gia_sx) AS total_gia_sx, SUM(gia_niem_yet) AS total_bang_gia, SUM(thuc_thu_mbt) AS total_thuc_thu ')
                    ->whereRaw("factory_id=$factory_id and $status_product")->first();
            $rs['total_count'] = $item->total_count;
            $rs['chi_phi'] = $item->total_gia_sx + $item->total_gia_sx*10/100 + $item->total_bang_gia*8/100 + $item->total_thuc_thu*9.83/100 ;
        }
        if ($factory_id == 3) {
            $item = DB::table('products')
                    ->selectRaw('COUNT(*)AS total_count, SUM(gia_sx) AS total_gia_sx, SUM(gia_niem_yet) AS total_bang_gia, SUM(thuc_thu_mbt) AS total_thuc_thu ')
                    ->whereRaw("factory_id=$factory_id and $status_product and may_sua_chua=1")->first();
            $item2 = DB::table('products')
                    ->selectRaw('COUNT(*)AS total_count, SUM(gia_sx) AS total_gia_sx, SUM(gia_niem_yet) AS total_bang_gia, SUM(thuc_thu_mbt) AS total_thuc_thu ')
                    ->whereRaw("factory_id=$factory_id and $status_product and may_sua_chua=2")->first();

            $rs['total_count'] = $item->total_count + $item2->total_count;
            $rs['chi_phi'] = $item->total_gia_sx + $item->total_bang_gia*(10.8 + 10.83)/100 + $item2->total_gia_sx + $item2->total_bang_gia*(12.8 + 10.83)/100 ;
        }
        return $rs;
    }
}