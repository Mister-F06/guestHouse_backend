<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *      path="/auth/login",
     *      operationId="authenticate",
     *      tags={"Auth"},
     *      summary="Authenticate user",
     *      description="Authenticate user and generate JWT token",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
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
     *                  required={"email", "password"}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="object",
     *                  @OA\Property(property="token", type="string"),
     *                  @OA\Property(property="user", type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="email", type="string"),
     *                      @OA\Property(property="created_at", type="string", format="date-time"),
     *                      @OA\Property(property="updated_at", type="string", format="date-time")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="bad credentials")
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

                    return $this->sendSuccessResponse($data);

                } else 
                    return $this->sendAccessError('bad credentials');
            } else 
                return $this->sendAccessError('please validated your email before to login');

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendServerError($th->getMessage());
        }
    }

    /**
     * @OA\Post(
     *      path="/auth/reset-password",
     *      operationId="resetPassword",
     *      tags={"Auth"},
     *      summary="Reset user password",
     *      description="Send password reset link to user's email",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      format="email",
     *                      example="user@example.com"
     *                  ),
     *                  required={"email"}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="password link sended")
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
    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|exists:users,email'
        ]);

        try {

            DB::beginTransaction();

            $user = User::where('email' , $data['email'])->first();

            $token_data = generateSignedRoute($data['email'] , 'reset.password.verify');

            $token_data['email_data']['url'] = updateSignedLink($token_data['email_data']['url'] , route('reset.password.verify') , 'reset/password');

            DB::table('password_reset_tokens')->insert([
                'token' => $token_data['token'],
                'email' => $request->email,
                'created_at' => Carbon::now()
            ]);

            $email_data = [
                'fullname'  => $user->firstname.' '.$user->lastname,
                'has_button'=> true,
                'button_text'=> 'Réinitialiser le mot de passe',
                'button_url' => $token_data['email_data']['url']
            ];

            Mail::to($data['email'])->send(new ResetPassword($email_data));

            DB::commit();

            return $this->sendSuccessResponse(['message' => 'password link sended']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendServerError($th->getMessage());
        }
    }

    /**
     * @OA\Put(
     *      path="/auth/reset-password",
     *      operationId="changePassword",
     *      tags={"Auth"},
     *      summary="Change user password",
     *      description="Change user password after reset",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
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
     *                      example="NewPassword@123",
     *                      minLength=8,
     *                      maxLength=255
     *                  ),
     *                  @OA\Property(
     *                      property="password_confirmation",
     *                      type="string",
     *                      format="password",
     *                      example="NewPassword@123"
     *                  ),
     *                  required={"email", "password", "password_confirmation"}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="password reset")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorized")
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
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'email'     => 'email|required|exists:users,email',
            'password'  => 'required|string|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/'
        ]);

        try {
            DB::beginTransaction();

            $user = User::where('email' , $data['email'])->first();

            $user->update(['password' => bcrypt($data['password'])]);

            DB::commit();

            return response()->json(['message' => 'password reset']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *      path="/reset-password/verify/signature",
     *      operationId="verifySignature",
     *      tags={"Auth"},
     *      summary="Verify password reset signature",
     *      description="Verify if the password reset signature is valid",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
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
     *          description="Valid signature",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Valid signature"),
     *              @OA\Property(property="email", type="string", example="user@example.com")
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
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string")
     *          )
     *      )
     * )
    */
    public function verifySignature(Request $request)
    {
        if (!$request->hasValidSignature()) 
            abort(401, 'invalid signature');

            try {

                DB::beginTransaction();
    
                $reset_password = DB::table('password_reset_tokens')
                    ->where('token', $request->get('token'))
                    ->first();
                    
               if(!$reset_password)
                   return $this->sendNotFound('token not found');
                   
              $email = $reset_password->email ;
              
              DB::table('password_reset_tokens')
                    ->where('token', $request->get('token'))
                    ->delete();
    
                DB::commit();
    
                return $this->sendSuccessResponse(['message' => "Valid signature" , 'email' => $email ]);

            } catch (\Throwable $th) {
                DB::rollBack();
                return $this->sendServerError($th->getMessage());
            }
    }

    /**
     * @OA\Post(
     *      path="/reset-password/resent/link",
     *      operationId="resentPasswordLink",
     *      tags={"Auth"},
     *      summary="Resend password reset link",
     *      description="Resend password reset link to user's email",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      format="email",
     *                      example="user@example.com"
     *                  ),
     *                  required={"email"}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="password link sended")
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
    public function resentPasswordLink(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|exists:password_reset_tokens,email'
        ]);
        
        try {

            DB::beginTransaction();
            
            $reset_password = DB::table('password_reset_tokens')
                                   ->where('email' , $data['email'])
                                   ->first();
                                   
            $reset_password->delete();

            $user = User::where('email' , $data['email'])
                           ->first();
                           
            $token_data = generateSignedRoute($data['email'] , 'reset.password.verify');

            $token_data['email_data']['url'] = updateSignedLink($token_data['email_data']['url'] , route('reset.password.verify') , 'reset/password');

            DB::table('password_reset_tokens')->insert([
                'token' => $token_data['token'],
                'email' => $request->email,
                'created_at' => Carbon::now()
            ]);

            $email_data = [
                'fullname'  => $user->firstname.' '.$user->lastname,
                'has_button'=> true,
                'reset_link'=> $token_data['email_data']['url'],
                'button_text'=> 'Réinitialiser le mot de passe',
                'button_url' => $token_data['email_data']['url']
            ];

            Mail::to($data['email'])->send(new ResetPassword($email_data));

            DB::commit();

            return $this->sendSuccessResponse(['message' => 'password link sended']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendServerError($th->getMessage());
        }
    }
}
