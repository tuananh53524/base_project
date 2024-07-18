<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Breadcrumb;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        // $filter = [];
        // if (!empty($request->username)) {
        //     $filter['username'] = $request->username;
        //     $query->where('users.username', 'LIKE', $request->username);
        // }

        // if (!empty($request->role_id)) {
        //     $filter['role_id'] = $request->role_id;
        //     $query->where('users.role_id', 'LIKE', $request->role_id);
        // }

        // if (!empty($request->email)) {
        //     $filter['email'] = $request->email;
        //     $query->where('users.email', '=', $request->email);
        // }

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
        $breadcrumb = Breadcrumb::build([
            'Users' => route('users.index'),
            'Create' => false
        ]);

        $roles = Role::getRoles();

        return view('dashboard.user.create', [
            'breadcrumb' => $breadcrumb,
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ], [
            'name.required' => 'Bạn chưa nhập name',
            'email.required' => 'Bạn chưa nhập email',
            'password.required' => 'Bạn chưa nhập mật khẩu'
        ]);

        $user = new User();
        $user->role_id = !empty($request->role_id) ? $request->role_id : config('app.roles.user');
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->avatar = $request->avatar;
        $user->password = Hash::make($request->password);
        $user->status = $request->status == 'on' ? 1 : 0;

        if ($user->save()) {
            return redirect()->route('users.index')->with('Success', 'The account has been successfully created');
        } else {
            return redirect()->route('users.index')->with('Error', 'Could not creat user');
        }
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
