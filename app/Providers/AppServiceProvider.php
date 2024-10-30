<?php 
namespace App\Providers;

use App\Repositories\UserRepository;
use AsfyCode\Container\Container;
use App\Http\Kernel;
use AsfyCode\Utils\Request;
use Illuminate\Database\Capsule\Manager;

class AppServiceProvider
{
    public function register(Container $app)
    {
        // Đăng kí các dịnh vụ vào container
        // Khi container được yêu cầu cung cấp một đối tượng thì nó sẽ trả về instance mới
        $app->bind(Kernel::class, function() use ($app) {
            return new Kernel($app);
        });

        // Khi container yêu cầu cung cấp một đối tượng Request thì callback sẽ gọi và nó sẽ  
        // tạo ra 1 đối tượng request đại diện cho request hiện tại
        $app->bind(Request::class, function() {
            return Request::capture();
        });
        
        $app->singleton('db',function(){
            return new Manager();
        });

        // config
        $app->bind(UserRepository::class, function() {
            return new UserRepository;
        });
    }
}