<?php 
namespace AsfyCode\Traits;

use AsfyCode\Utils\Request;

trait MiddleBefore
{
    protected function previousUrl(Request $request){

        if(strpos($request->uri(),'/api') === 0){
            return;
        }

        $previous_url = $request->session()->get('previous_url') ?? [];

        if (count($previous_url) === 0) {
            $previous_url[] = [
                'path' => $request->uri(),
                'method'  => $request->method()
            ];
        }else{
            $previous_url = array_values($previous_url);
            $pre = end($previous_url);
            if(($pre['path'] != $request->uri()) || ($pre['method'] != $request->method())){
                $previous_url[] = [
                    'path' => $request->uri(),
                    'method'  => $request->method()
                ];
            }
        }

        if(count($previous_url) > 5){
            $previous_url = array_slice($previous_url, -5, 5, true);
        }

        $request->session()->set('previous_url',$previous_url); 
    }

    protected function csrfToken(Request $request){
        if($request->method() !== 'GET'){
            if($request->ajax()){
                $csrfToken = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : NULL;
            }else{
                $csrfToken = $request->input('csrf_token') ?? NULL;
            }
            if (!session()->has("csrf_token") || is_null($csrfToken) || !hash_equals(session()->get("csrf_token"), $csrfToken)) {
                abort(419);
            }else{
                if(!$request->ajax()){
                    session()->remove("csrf_token");
                }
            }
        }
    }
}