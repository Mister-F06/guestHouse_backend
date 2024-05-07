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

    /**
     * @OA\Post(
     *      path="/auth/register",
     *      operationId="register",
     *      tags={"Auth"},
     *      summary="Register user",
     *      description="Register user and send email verification link",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="firstname",
     *                      type="string",
     *                      example="John"
     *                  ),
     *                  @OA\Property(
     *                      property="lastname",
     *                      type="string",
     *                      example="Doe"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      format="email",
     *                      example="user@example.com"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      format="password",
     *                      example="password"
     *                  ),
     *                  required={"firstname", "lastname", "email", "password"}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="user registered")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string")
     *          )
     *      )
     * )
    */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();
            $role_id = Role::where('name' , 'manager')->first()->id;
            $data['role_id'] = $role_id;
            $data['password'] = bcrypt($data['password']);


            $user = User::create($data);

            $token_data = generateSignedRoute($data['email']);

            $token_data['email_data']['url'] = updateSignedLink($token_data['email_data']['url'] , route('email.verify') , 'verify/email');

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

    /**
     * @OA\Post(
     *      path="/auth/verify/email",
     *      operationId="verifyEmail",
     *      tags={"Auth"},
     *      summary="Verify email",
     *      description="Verify email using the provided token",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="token",
     *                      type="string",
     *                      example="eyJpdiI6InlJSzJ4UnlEbUw4OEg0b0hBNEJBYWc9PSIsInZhbHVlIjozSUp0WlwvVEI5M2V2aEtpWlNnVDczQT09IiwibWFjIjoiYmNjYTYxMTMzZmRlYzY0MjJjYzZjNzQ4YTA4NTI0ZjE3ZDc5Y2FiNzZmN2IyNTBiOWY5NDI5ODNmZGM4MzQzNyJ9"
     *                  ),
     *                  required={"token"}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="email verified")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="invalid signature")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="token not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="email doesn't exist")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string")
     *          )
     *      )
     * )
    */
    public function verifyEmail(Request $request)
    {
        if (!$request->hasValidSignature()) 
            abort(401 , 'invalid signature');
            
        $token = $request->get('token');

        $verified_email = EmailVerified::where('token', $token)->first();

        if (!$verified_email)
            return response()->json(['message' => 'token not found'], 404);

        try {

            DB::beginTransaction();

            $user = User::where('email' , $verified_email->email)->first();
                
            if(!$user)
                return response()->json(['message' => "email doesn't exist"] , 404);

            $user->update(['email_verified_at' => now(), 'is_enbaled' => true]);

            $verified_email->delete();

            DB::commit();

            return response()->json(['message' => 'email verified'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    /**
     * @OA\Put(
     *      path="/resent/verify/link",
     *      operationId="resentLink",
     *      tags={"Auth"},
     *      summary="Resend verification link",
     *      description="Resend verification link to user's email",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="token",
     *                      type="string",
     *                      example="eyJpdiI6InlJSzJ4UnlEbUw4OEg0b0hBNEJBYWc9PSIsInZhbHVlIjozSUp0WlwvVEI5M2V2aEtpWlNnVDczQT09IiwibWFjIjoiYmNjYTYxMTMzZmRlYzY0MjJjYzZjNzQ4YTA4NTI0ZjE3ZDc5Y2FiNzZmN2IyNTBiOWY5NDI5ODNmZGM4MzQzNyJ9"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      format="email",
     *                      example="user@example.com"
     *                  ),
     *                  required={"token", "email"}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="link verification sent")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string")
     *          )
     *      )
     * )
    */
    public function resentLink(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string|exists:email_verifieds,token'
        ]);

        try {
            DB::beginTransaction();

            $email_verified = EmailVerified::where('token' , $data['token'])->first();

            $user = User::where('email' , $email_verified->email)->first();

            $token_data  = generateSignedRoute($user->email);

            $token_data['email_data']['url'] = updateSignedLink($token_data['email_data']['url'] , route('email.verify') , 'verify/email');

            $data_verified = [
                'token' => $token_data['token'],
                'email' => $email_verified->email
            ];

            $email_verified->delete();
            
            EmailVerified::create($data_verified);

            $email_data = [
                'fullname'      => $user->firstname.' '. $user->lastname,
                'has_button'    => true,
                'button_text'   => 'Vérifier votre email',
                'button_url'    => $token_data['email_data']['url']
            ];

            Mail::to($user->email)->send(new EmailVerify($email_data));

            DB::commit();

            return response()->json(['message' => 'link verification sent']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }



}
