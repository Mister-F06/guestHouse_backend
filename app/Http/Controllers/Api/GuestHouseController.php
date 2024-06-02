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
       try {
        $guest_houses = GuestHouse::all();
        return $this->sendSuccessResponse($guest_houses);
       } catch (\Throwable $th) {
            return $this->sendServerError($th->getMessage());
       }
    }


    public function search(Request $request)
    {
        $data = $request->all();

        if(!$data)
            abort(419 , 'empty search query');

        $guest_houses = GuestHouse::where($data)
                                    ->get();
        return $guest_houses;
    }

    /**
     * Display a listing of the resource for specific user.
     */
    public function indexManager()
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $guest_houses = GuestHouse::where('user_id' , $user->id)
                                        ->get();
            return $this->sendSuccessResponse($guest_houses);
        } catch (\Throwable $th) {
            return $this->sendServerError($th->getMessage());
        }
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
        return $guestHouse;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, GuestHouse $guestHouse)
    {
        $data = $request->validated();
        
        try {
            
            DB::beginTransaction();

            $guestHouse->update($data);

            if ($request->hasFile('cover')) {

                $media = $guestHouse->getFirstMedia('Cover');

                if($media)
                    $media->delete();

                StoreFile::addFile($data['cover'], 'Cover' , $guestHouse);
            }

            if($request->hasFile('pictures')){

                foreach ($guestHouse->getMedia('Pictures') as $key => $media) {
                    
                    $media->delete();
                }

                foreach ($data['pictures'] as $key => $media) 
                    StoreFile::addFile($media , 'Pictures' , $guestHouse);
                
            }

            if ($request->hasFile('videos')) {
                foreach ($guestHouse->getMedia('Videos') as $key => $media) {
                    $media->delete();
                }

                foreach ($data['videos'] as $key => $video) 
                   StoreFile::addFile($video , 'Videos' , $guestHouse);

            }

            DB::commit();

            if($guestHouse->wasChanged())
                return $this->sendSuccessResponse(['message' => 'guest house updated' , 'data' => $guestHouse]);
            else 
                return $this->sendSuccessResponse(['message' => 'nothing updated' , 'data' => $guestHouse]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendServerError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GuestHouse $guestHouse)
    {
        try {
            DB::beginTransaction();
            $media = $guestHouse->getFirstMedia('Cover');

            if($media)
                $media->delete();


            foreach ($guestHouse->getMedia('Pictures') as $key => $media) {
                    
                $media->delete();
            }

            foreach ($guestHouse->getMedia('Videos') as $key => $media) {
                $media->delete();
            }

            $guestHouse->delete();

            DB::commit();

            return $this->sendSuccessResponse(['message' => 'guest house registered']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendServerError($th->getMessage());
        }

    }
}
