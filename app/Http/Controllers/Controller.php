<?php

namespace App\Http\Controllers;


/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Guest House Platform API",
 *      description="Description de votre API",
 *      @OA\Contact(
 *          email="contact@guesthouse.com"
 *      )
 * )
 */
abstract class Controller
{
     /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendSuccessResponse($response  , $code=200 )
    {

        return response()->json($response, $code);
    }


    /**
     * return unhautorized error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendAccessError($error)
    {
    	$response = [
            'error' => $error
        ];

        return response()->json($response, 401);
    }


    /**
     * server not found response method error.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendNotFound($error){
    	$response = [
            'error' => $error
        ];

        return response()->json($response, 404);
    }


    /**
     * server response method error.
     *
     * @return \Illuminate\Http\Response
    */
    public function sendServerError($error){
    	$response = [
            'error' => $error
        ];

        return response()->json($response, 500);
    }
}
