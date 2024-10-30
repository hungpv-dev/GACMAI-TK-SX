<?php
namespace App\Repositories;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WEB\StatusCustomer;
use App\Models\Customer;
use App\Models\CustomerNotify;
use App\Models\CustomerStatus;
use App\Models\User;
use DateTime;
use Illuminate\Database\Capsule\Manager;

class UserRepository extends Controller
{

    public function get()
    {
        $user = user();
        $users = User::where("group_id", "=", $user->group_id)->get();
        return $users;
    }

    public function customerExpired()
    {
        $status = CustomerStatus::orderBy('sort', 'asc')->get();
        $response = [];
        foreach ($status as $sta) {
            $data = $this->getCustomerExpiredForStatus($sta);
            $response[] = $data;
        }
        $response = array_filter($response, function ($item) {
            return $item->count_old > 0;
        });
        return $response;
    }
    public function getCustomerExpiredForStatus($status)
    {
        $statusNotify = CustomerNotify::where('status_id', $status->id)->orderBy('time_notify', 'asc')->get();
        if (!$statusNotify->isEmpty()) {
            $first = (clone $statusNotify)->first();
            $groupIds = (clone $statusNotify)->pluck('group_id');
            $timeThreshold = (new DateTime())->modify("-{$first->time_notify} days")->format("Y-m-d H:i:s");
            $sql = 'SELECT COUNT(*) as customer_count FROM `customers` 
                WHERE EXISTS (
                    SELECT * 
                    FROM `users` 
                    WHERE `customers`.`user_id` = `users`.`id` 
                    AND `group_id` IN (' . implode(',', array_fill(0, count($groupIds), '?')) . ')
                ) 
                AND `status_id` = ? 
                AND `id` IN (
                    SELECT `customer_id` 
                    FROM `customer_logs` 
                    WHERE `created_at` = (
                        SELECT MAX(`created_at`) 
                        FROM `customer_logs` 
                        WHERE `customer_logs`.`customer_id` = `customers`.`id`
                        HAVING `created_at` < ?
                    )
                )';
            $result = Manager::select($sql, array_merge($groupIds->toArray(), [$status->id, $timeThreshold]));
            $count = $result[0]->customer_count;
        } else {
            $count = 0;
        }
        $status->count_old = $count;
        return $status;
    }

    public function customerSchedule()
    {
        $customer = Customer::where('status_id', status('customer_schelude'))
            ->whereDate('schedule', '<=', now())->count();
        $status = CustomerStatus::find(status('customer_schelude'));
        $status->count_old = $customer;
        return $status;
    }
}
