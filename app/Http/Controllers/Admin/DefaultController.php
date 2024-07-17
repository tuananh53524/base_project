<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DefaultController extends Controller
{
    //
    public function index()
    {
        return view('dashboard.default.index');
    }

    public function download(Request $request)
    {
        $path = storage_path() . '/' . 'logs' . '/' . $request->file_name;
        if (file_exists($path)) {
            return Response::download($path);
        }

        return redirect('/admin');
    }
}
