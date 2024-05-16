<?php

namespace App\Http\Controllers\Api;


use App\Models\GuestHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\GuestHouse\StoreRequest;
use App\Http\Requests\Api\GuestHouse\UpdateRequest;
use App\Managers\StoreFile;

class GuestHouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $guest_houses = GuestHouse::all();

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
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        try {

            DB::beginTransaction();

            $data['user_id'] = $request->user()->id;
            $guestHouse = GuestHouse::create($data);

            // Store Media
            StoreFile::addFile($data['cover'] , 'Cover' , $guestHouse);

            foreach ($data['pictures'] as $key => $picture) 
                StoreFile::addFile($picture , 'Pictures' , $guestHouse);

            foreach ($data['videos'] as $key => $video) 
                StoreFile::addFile($video , 'Videos' , $guestHouse);

            DB::commit();
            return $this->sendSuccessResponse(['message' => 'guest house created']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendServerError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GuestHouse $guestHouse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GuestHouse $guestHouse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, GuestHouse $guestHouse)
    {
        $data = $request->validated();

        try {
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GuestHouse $guestHouse)
    {
        //
    }
}
