<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * @OA\Get(
     *      path="/me",
     *      operationId="me",
     *      tags={"Users"},
     *      summary="Get connected user information",
     *      description="Returns information about the connected user",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="firstname", type="string", example="Admin"),
     *              @OA\Property(property="lastname", type="string", example="GuestHouse"),
     *              @OA\Property(property="email", type="string", format="email", example="admin@mail.com"),
     *              @OA\Property(property="telephone", type="string", example="44444444"),
     *              @OA\Property(property="is_enabled", type="boolean", example=true),
     *              @OA\Property(property="role_id", type="integer", example=1),
     *              @OA\Property(
     *                  property="role",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="admin"),
     *                  @OA\Property(property="label", type="string", example="Administrateur")
     *              )
     *          )
     *      ),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
    */
    public function me(Request $request)
    {
        $user = $request->user();
         $user->role = $user->role->name;
        return $user;
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
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
