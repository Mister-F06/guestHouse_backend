<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Mail\EmailVerify;
use App\Models\EmailVerified;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

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

            $token_data = $this->generateSignedRoute($data['email']);

            $email_data = [
                'fullname'      => $data['firstname'].' '. $data['lastname'],
                'has_button'    => true,
                'button_text'   => 'Vérifier votre email',
                'button_url'    => $token_data['email_data']['url']
            ];

            $verified_data = [
                'email' =>  $data['email'],
                'token' =>  $token_data['token']
            ];

            EmailVerified::create($verified_data);

            Mail::to($data['email'])->send(new EmailVerify($email_data));

            DB::commit();

            return response()->json(['message' => 'user registered']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }


    public function verifyEmail(Request $request)
    {
        if (!$request->hasValidSignature()) 
            return response()->json(['message' => 'invalid signature'], 401);
            
        $token = $request->get('token');

        $verified_email = EmailVerified::where('token', $token)->first();

        if (!$verified_email)
            return response()->json(['message' => 'token not found'], 404);

        try {

            DB::beginTransaction();

            $user = User::where('email' , $request->email)->first();
                
            if(!$user)
                return response()->json(['message' => "email doesn't exist"] , 404);

            $user->update(['email_verified_at' => now(), 'is_active' => true]);

            $verified_email->delete();

            DB::commit();

            return response()->json(['message' => 'email verified'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    private function generateSignedRoute($email , $mode = 'email.verify')
    {
        $token = Str::random(40);

        $email_data = [
            'url' => URL::temporarySignedRoute(
                $mode,
                now()->addHour(),
                ['token' => $token]
            ),
            'site_url' => config('app.url')
        ];

        return ['email_data' => $email_data, 'token' => $token];
    }

}
