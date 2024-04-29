<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    
    public function authenticate(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string'
        ]);

        try {

            // Retrieve user attempted to loged in
            $user = User::where('email' , $data['email'])->first();

            if ($user->is_enbaled) {
                if(Hash::check($request->password, $user->password)){ // verify password

                    $data = [ // Generate login data
                        'token' => $user->createToken($data['email'])
                                        ->plainTextToken,
                        'user'  => $user 
                    ];

                    return response()->json(['message' => $data]);

                } else 
                    return response()->json(['message' => 'bad credentials'] , 401);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
