<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    //
    public function execCommand(Request $request)
    {
        $command = $request->command;
        $response = ['status' => true, 'msg' => 'Xoá cache và config thành công!', 'task' => 'clearCacheAndConfig'];
        if (Auth::user()->role_id != config('app.roles.root')) {
            $response['status'] = false;
            $response['msg'] = 'Bạn không được cấp quyền để chạy chức năng này!';
        } else {
            try {
                if (empty($command)) {
                    $commands = ['route:cache', 'config:cache', 'cache:clear', 'view:clear'];
                    foreach ($commands as $command) {
                        Artisan::call($command);
                    }
                    Cache::flush();
                } else {
                    Artisan::call($command);
                    Log::info(__FILE__ . ":" . __LINE__ . " output: " . Artisan::output());
                    $response['msg'] = "Command <b>'$command'</b> đã chạy thành công!'";
                }
            } catch (\Exception $ex) {
                Log::error(__FILE__ . ":" . __LINE__ . " >> " . $ex->getMessage());
                $response['status'] = false;
                $response['msg'] = $ex->getMessage();
            }
        }

        return response()->json($response);
    }

    public function phpinfo()
    {
        if (Auth::user()->role_id == config('app.roles.root')) {
            return phpinfo();
        }
    }
}
