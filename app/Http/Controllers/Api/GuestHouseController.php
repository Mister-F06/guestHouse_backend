<?php

namespace App\Http\Controllers\Api;


use App\Models\GuestHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\GuestHouse\StoreRequest;
use App\Http\Requests\Api\GuestHouse\UpdateRequest;
use App\Mail\AdminNotifyGuestHouseCreated;
use App\Mail\HouseStatusNotify;
use App\Managers\StoreFile;
use App\Models\GuestHouseRejected;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class GuestHouseController extends Controller
{

    /**
    * @OA\Get(
    *      path="/guest_houses",
    *      operationId="getGuestHouses",
    *      tags={"GuestHouse"},
    *      summary="Get list of guest houses",
    *      description="Returns a list of guest houses",
    *      security={{"bearerAuth":{}}},
    *      @OA\Response(
    *          response=200,
    *          description="Successful operation",
    *          @OA\JsonContent(
    *              type="array",
    *              @OA\Items(
    *                  @OA\Property(property="id", type="integer", example=1),
    *                  @OA\Property(property="name", type="string", example="Lovely Guest House"),
    *                  @OA\Property(property="slug", type="string", nullable=true, example="lovely-guest-house"),
    *                  @OA\Property(property="description", type="string", example="A lovely guest house with beautiful views."),
    *                  @OA\Property(property="price", type="integer", example=100),
    *                  @OA\Property(property="address", type="string", example="123 Guest House Lane"),
    *                  @OA\Property(property="map_link", type="string", nullable=true, example="https://maps.example.com/guesthouse"),
    *                  @OA\Property(property="bedrooms_nbr", type="integer", example=3),
    *                  @OA\Property(property="beds_nbr", type="integer", example=4),
    *                  @OA\Property(property="toilets_nbr", type="integer", example=2),
    *                  @OA\Property(property="bathrooms_nbr", type="integer", example=2),
    *                  @OA\Property(property="has_kitchen", type="boolean", example=true),
    *                  @OA\Property(property="has_pool", type="boolean", example=false),
    *                  @OA\Property(property="has_air_conditionner", type="boolean", example=true),
    *                  @OA\Property(property="has_jacuzzi", type="boolean", example=false),
    *                  @OA\Property(property="has_washing_machine", type="boolean", example=true),
    *                  @OA\Property(property="has_car", type="boolean", example=false),
    *                  @OA\Property(property="has_parking", type="boolean", example=true),
    *                  @OA\Property(property="status", type="string", enum={"pending_validation", "validated", "rejected"}, example="validated"),
    *                  @OA\Property(property="is_enabled", type="boolean", example=true),
    *                  @OA\Property(property="user_id", type="integer", example=1),
    *                  @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-01T09:38:45.000000Z"),
    *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-01T09:38:45.000000Z")
    *              )
    *          )
    *      ),
    *      @OA\Response(response=500, description="Server error")
    * )
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


    /**
     * @OA\Get(
     *      path="/guest_houses/search",
     *      operationId="searchGuestHouses",
     *      tags={"GuestHouse"},
     *      summary="Search for guest houses based on filters",
     *      description="Returns a list of guest houses matching the specified filters",
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="string"),
     *          description="Name of the guest house"
     *      ),
     *      @OA\Parameter(
     *          name="price",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer"),
     *          description="Price of the guest house"
     *      ),
     *      @OA\Parameter(
     *          name="address",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="string"),
     *          description="Address of the guest house"
     *      ),
     *      @OA\Parameter(
     *          name="bedrooms_nbr",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer"),
     *          description="Number of bedrooms"
     *      ),
     *      @OA\Parameter(
     *          name="beds_nbr",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer"),
     *          description="Number of beds"
     *      ),
     *      @OA\Parameter(
     *          name="toilets_nbr",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer"),
     *          description="Number of toilets"
     *      ),
     *      @OA\Parameter(
     *          name="bathrooms_nbr",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="integer"),
     *          description="Number of bathrooms"
     *      ),
     *      @OA\Parameter(
     *          name="has_kitchen",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="boolean"),
     *          description="Whether the guest house has a kitchen"
     *      ),
     *      @OA\Parameter(
     *          name="has_pool",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="boolean"),
     *          description="Whether the guest house has a pool"
     *      ),
     *      @OA\Parameter(
     *          name="has_air_conditionner",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="boolean"),
     *          description="Whether the guest house has air conditioning"
     *      ),
     *      @OA\Parameter(
     *          name="has_jacuzzi",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="boolean"),
     *          description="Whether the guest house has a jacuzzi"
     *      ),
     *      @OA\Parameter(
     *          name="has_washing_machine",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="boolean"),
     *          description="Whether the guest house has a washing machine"
     *      ),
     *      @OA\Parameter(
     *          name="has_car",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="boolean"),
     *          description="Whether the guest house provides a car"
     *      ),
     *      @OA\Parameter(
     *          name="has_parking",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="boolean"),
     *          description="Whether the guest house has parking"
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="string", enum={"pending_validation", "validated", "rejected"}),
     *          description="Status of the guest house"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Lovely Guest House"),
     *                  @OA\Property(property="slug", type="string", nullable=true, example="lovely-guest-house"),
     *                  @OA\Property(property="description", type="string", example="A lovely guest house with beautiful views."),
     *                  @OA\Property(property="price", type="integer", example=100),
     *                  @OA\Property(property="address", type="string", example="123 Guest House Lane"),
     *                  @OA\Property(property="map_link", type="string", nullable=true, example="https://maps.example.com/guesthouse"),
     *                  @OA\Property(property="bedrooms_nbr", type="integer", example=3),
     *                  @OA\Property(property="beds_nbr", type="integer", example=4),
     *                  @OA\Property(property="toilets_nbr", type="integer", example=2),
     *                  @OA\Property(property="bathrooms_nbr", type="integer", example=2),
     *                  @OA\Property(property="has_kitchen", type="boolean", example=true),
     *                  @OA\Property(property="has_pool", type="boolean", example=false),
     *                  @OA\Property(property="has_air_conditionner", type="boolean", example=true),
     *                  @OA\Property(property="has_jacuzzi", type="boolean", example=false),
     *                  @OA\Property(property="has_washing_machine", type="boolean", example=true),
     *                  @OA\Property(property="has_car", type="boolean", example=false),
     *                  @OA\Property(property="has_parking", type="boolean", example=true),
     *                  @OA\Property(property="status", type="string", enum={"pending_validation", "validated", "rejected"}, example="validated"),
     *                  @OA\Property(property="is_enabled", type="boolean", example=true),
     *                  @OA\Property(property="user_id", type="integer", example=1),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-01T09:38:45.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-01T09:38:45.000000Z")
     *              )
     *          )
     *      ),
     *      @OA\Response(response=419, description="Empty search query"),
     *      @OA\Response(response=500, description="Server error")
     * )
    */
    public function search(Request $request)
    {
        $data = $request->all();

        if(!$data)
            abort(419 , 'empty search query');

        $guest_houses = GuestHouse::enabled()
                                    ->where($data)
                                    ->get();
        return $guest_houses;
    }

    

    /**
     * @OA\Get(
     *      path="/guest_houses/manager",
     *      operationId="getManagerGuestHouses",
     *      tags={"GuestHouse"},
     *      summary="Get a listing of guest houses for the authenticated user",
     *      description="Returns a list of guest houses that belong to the authenticated user",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Lovely Guest House"),
     *                  @OA\Property(property="slug", type="string", nullable=true, example="lovely-guest-house"),
     *                  @OA\Property(property="description", type="string", example="A lovely guest house with beautiful views."),
     *                  @OA\Property(property="price", type="integer", example=100),
     *                  @OA\Property(property="address", type="string", example="123 Guest House Lane"),
     *                  @OA\Property(property="map_link", type="string", nullable=true, example="https://maps.example.com/guesthouse"),
     *                  @OA\Property(property="bedrooms_nbr", type="integer", example=3),
     *                  @OA\Property(property="beds_nbr", type="integer", example=4),
     *                  @OA\Property(property="toilets_nbr", type="integer", example=2),
     *                  @OA\Property(property="bathrooms_nbr", type="integer", example=2),
     *                  @OA\Property(property="has_kitchen", type="boolean", example=true),
     *                  @OA\Property(property="has_pool", type="boolean", example=false),
     *                  @OA\Property(property="has_air_conditionner", type="boolean", example=true),
     *                  @OA\Property(property="has_jacuzzi", type="boolean", example=false),
     *                  @OA\Property(property="has_washing_machine", type="boolean", example=true),
     *                  @OA\Property(property="has_car", type="boolean", example=false),
     *                  @OA\Property(property="has_parking", type="boolean", example=true),
     *                  @OA\Property(property="status", type="string", enum={"pending_validation", "validated", "rejected"}, example="validated"),
     *                  @OA\Property(property="is_enabled", type="boolean", example=true),
     *                  @OA\Property(property="user_id", type="integer", example=1),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-01T09:38:45.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-01T09:38:45.000000Z")
     *              )
     *          )
     *      ),
     *      @OA\Response(response=500, description="Server error")
     * )
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
     * @OA\Post(
     *     path="/guest_houses",
     *     operationId="storeGuestHouse",
     *     tags={"GuestHouse"},
     *     summary="Store a newly created guest house in storage",
     *     description="Create a new guest house and store it in the database",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "price", "address", "bedrooms_nbr", "beds_nbr", "toilets_nbr", "bathrooms_nbr", "has_kitchen", "has_pool", "has_air_conditionner", "has_jacuzzi", "has_washing_machine", "has_car", "has_parking", "cover", "pictures", "videos"},
     *             @OA\Property(property="name", type="string", example="Lovely Guest House"),
     *             @OA\Property(property="description", type="string", example="A lovely guest house with beautiful views."),
     *             @OA\Property(property="price", type="integer", example=100),
     *             @OA\Property(property="address", type="string", example="123 Guest House Lane"),
     *             @OA\Property(property="map_link", type="string", nullable=true, example="https://maps.example.com/guesthouse"),
     *             @OA\Property(property="bedrooms_nbr", type="integer", example=3),
     *             @OA\Property(property="beds_nbr", type="integer", example=4),
     *             @OA\Property(property="toilets_nbr", type="integer", example=2),
     *             @OA\Property(property="bathrooms_nbr", type="integer", example=2),
     *             @OA\Property(property="has_kitchen", type="boolean", example=true),
     *             @OA\Property(property="has_pool", type="boolean", example=false),
     *             @OA\Property(property="has_air_conditionner", type="boolean", example=true),
     *             @OA\Property(property="has_jacuzzi", type="boolean", example=false),
     *             @OA\Property(property="has_washing_machine", type="boolean", example=true),
     *             @OA\Property(property="has_car", type="boolean", example=false),
     *             @OA\Property(property="has_parking", type="boolean", example=true),
     *             @OA\Property(property="cover", type="string", format="binary"),
     *             @OA\Property(
     *                 property="pictures",
     *                 type="array",
     *                 @OA\Items(type="string", format="binary")
     *             ),
     *             @OA\Property(
     *                 property="videos",
     *                 type="array",
     *                 @OA\Items(type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Guest house created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="guest house created")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server error")
     *         )
     *     )
     * )
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

            Mail::to(config('mail.from.address'))->send(new AdminNotifyGuestHouseCreated());

            DB::commit();

            return $this->sendSuccessResponse(['message' => 'guest house created']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendServerError($th->getMessage());
        }
    }

    


    /**
     * @OA\Get(
     *     path="/guest_houses/{id}",
     *     operationId="getGuestHouseById",
     *     tags={"GuestHouse"},
     *     summary="Display the specified guest house",
     *     description="Returns a guest house by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the guest house to display"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Lovely Guest House"),
     *             @OA\Property(property="slug", type="string", example="lovely-guest-house"),
     *             @OA\Property(property="description", type="string", example="A lovely guest house with beautiful views."),
     *             @OA\Property(property="price", type="integer", example=100),
     *             @OA\Property(property="address", type="string", example="123 Guest House Lane"),
     *             @OA\Property(property="map_link", type="string", nullable=true, example="https://maps.example.com/guesthouse"),
     *             @OA\Property(property="bedrooms_nbr", type="integer", example=3),
     *             @OA\Property(property="beds_nbr", type="integer", example=4),
     *             @OA\Property(property="toilets_nbr", type="integer", example=2),
     *             @OA\Property(property="bathrooms_nbr", type="integer", example=2),
     *             @OA\Property(property="has_kitchen", type="boolean", example=true),
     *             @OA\Property(property="has_pool", type="boolean", example=false),
     *             @OA\Property(property="has_air_conditionner", type="boolean", example=true),
     *             @OA\Property(property="has_jacuzzi", type="boolean", example=false),
     *             @OA\Property(property="has_washing_machine", type="boolean", example=true),
     *             @OA\Property(property="has_car", type="boolean", example=false),
     *             @OA\Property(property="has_parking", type="boolean", example=true),
     *             @OA\Property(property="status", type="string", example="pending_validation"),
     *             @OA\Property(property="is_enabled", type="boolean", example=false),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-01T09:38:45.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-01T09:38:45.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Guest house not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Guest house not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
    */
    public function show(GuestHouse $guestHouse)
    {
        return $guestHouse;
    }


    
    /**
     * @OA\Put(
     *     path="/guest-houses/{id}",
     *     operationId="updateGuestHouse",
     *     tags={"GuestHouse"},
     *     summary="Update the specified guest house",
     *     description="Updates a guest house's details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the guest house to update"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255, example="Lovely Guest House"),
     *             @OA\Property(property="description", type="string", example="A lovely guest house with beautiful views."),
     *             @OA\Property(property="price", type="integer", example=100),
     *             @OA\Property(property="address", type="string", example="123 Guest House Lane"),
     *             @OA\Property(property="map_link", type="string", nullable=true, example="https://maps.example.com/guesthouse"),
     *             @OA\Property(property="bedrooms_nbr", type="integer", example=3),
     *             @OA\Property(property="beds_nbr", type="integer", example=4),
     *             @OA\Property(property="toilets_nbr", type="integer", example=2),
     *             @OA\Property(property="bathrooms_nbr", type="integer", example=2),
     *             @OA\Property(property="has_kitchen", type="boolean", example=true),
     *             @OA\Property(property="has_pool", type="boolean", example=false),
     *             @OA\Property(property="has_air_conditionner", type="boolean", example=true),
     *             @OA\Property(property="has_jacuzzi", type="boolean", example=false),
     *             @OA\Property(property="has_washing_machine", type="boolean", example=true),
     *             @OA\Property(property="has_car", type="boolean", example=false),
     *             @OA\Property(property="has_parking", type="boolean", example=true),
     *             @OA\Property(property="cover", type="string", format="binary"),
     *             @OA\Property(property="pictures", type="array", @OA\Items(type="string", format="binary")),
     *             @OA\Property(property="videos", type="array", @OA\Items(type="string", format="binary"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="guest house updated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Guest house not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Guest house not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
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
     * @OA\Patch(
     *     path="/guest-houses/{id}/status",
     *     operationId="changeGuestHouseStatus",
     *     tags={"GuestHouse"},
     *     summary="Update status for the specified guest house",
     *     description="Change the status of a guest house to either 'rejected' or 'validated'",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the guest house to update the status"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"rejected", "validated"},
     *                 example="validated"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="status changed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Guest house not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Guest house not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
    */
    public function changeStatus(Request $request , GuestHouse $guestHouse)
    {
        try {

            DB::beginTransaction();

            $data = $request->validate([
                'status'    => 'required|string|'.Rule::in(['rejected' , 'validated']),
                'reasons'   => 'required_if:status,rejected|string'
            ]);
    
            $guestHouse->update(['status' => $data['status']]);


            if($guestHouse->status === 'validated')
                $guestHouse->guestHouseRejecteds()->delete();
            else 
                GuestHouseRejected::create([
                    'reasons'   => $data['reasons'],
                    'date'      => Carbon::now(),
                    'guest_house_id' => $guestHouse->id  
                ]);

            $email_data = [
                'fullname'  => $guestHouse->user->firstname. ' '. $guestHouse->user->lastname,
                'status'    => $guestHouse->status,
                'house_name'=> $guestHouse->name,
                'reasons'   => $guestHouse->status == 'rejected' ? $data['reasons'] : null
            ];

            Mail::to($guestHouse->user->email)->send(new HouseStatusNotify($email_data) , $email_data);

            DB::commit();
    
            return $this->sendSuccessResponse('status changed');
    
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendServerError($th->getMessage());
        }

    }



    /**
     * @OA\Patch(
     *     path="/guest-houses/{id}/visibility",
     *     operationId="changeGuestHouseVisibility",
     *     tags={"GuestHouse"},
     *     summary="Update visibility for the specified guest house",
     *     description="Change the visibility status of a guest house to either true or false",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the guest house to update the visibility"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="visibility",
     *                 type="boolean",
     *                 example=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Visibility changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="visibility changed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Guest house not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Guest house not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
    */
    public function changeVisibility(Request $request , GuestHouse $guestHouse)
    {
        $data = $request->validate([
            'visibility'    => 'required|boolean'
        ]);

        $guestHouse->update(['is_enabled' => $data['visibility']]);

        return $this->sendSuccessResponse('visibility changed');
    }

    

    /**
     * @OA\Delete(
     *     path="/guest-houses/{id}",
     *     operationId="deleteGuestHouse",
     *     tags={"GuestHouse"},
     *     summary="Remove the specified guest house",
     *     description="Delete a guest house along with its associated media files (cover, pictures, and videos) from storage",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the guest house to delete"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Guest house deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="guest house deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Guest house not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Guest house not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
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
