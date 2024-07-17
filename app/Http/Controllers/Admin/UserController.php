<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Breadcrumb;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $breadcrumb = Breadcrumb::build([
            'Users' => route('users.index')
        ]);

        $query = User::leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.avatar',
                'users.phone',
                'users.status',
                'roles.name AS role_name'
            );

        $filter = [];
        if (!empty($request->username)) {
            $filter['username'] = $request->username;
            $query->where('users.username', 'LIKE', $request->username);
        }

        if (!empty($request->role_id)) {
            $filter['role_id'] = $request->role_id;
            $query->where('users.role_id', 'LIKE', $request->role_id);
        }

        if (!empty($request->email)) {
            $filter['email'] = $request->email;
            $query->where('users.email', '=', $request->email);
        }

        $records = $query->orderBy('users.created_at', 'desc')->paginate(20);
        $users = DB::table('users')->where('status', 1)->where('role_id', '<>', 1)->get();

        return view('dashboard.user.index', [
            'breadcrumb' => $breadcrumb,
            "records" => $records,
            // 'filter' => $filter,
            // 'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
