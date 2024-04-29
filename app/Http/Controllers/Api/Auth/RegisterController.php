<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();
            $role_id = Role::where('name' , 'manager')->first()->id;
            $data['role_id'] = $role_id;
            $data['password'] = bcrypt($data['password']);
            $user = User::create($data);
            return $user;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
