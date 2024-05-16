<?php

namespace App\Http\Requests\Api\GuestHouse;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                  => 'sometimes|string|max:255|unique:guest_houses,name,' . $this->guest_house->id,
            'description'           => 'sometimes|string',
            'price'                 => 'sometimes|integer',
            'address'               => 'sometimes|string',
            'map_link'              => 'nullable|string',
            'bedrooms_nbr'          => 'sometimes|integer',
            'beds_nbr'              => 'sometimes|integer',
            'toilets_nbr'           => 'sometimes|integer',
            'bathrooms_nbr'         => 'sometimes|integer',
            'has_kitchen'           => 'sometimes|boolean',
            'has_pool'              => 'sometimes|boolean',
            'has_air_conditionner'  => 'sometimes|boolean',
            'has_jacuzzi'           => 'sometimes|boolean',
            'has_washing_machine'   => 'sometimes|boolean',
            'has_car'               => 'sometimes|boolean',
            'has_parking'           => 'sometimes|boolean',
            'cover'                 => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048',
            'pictures'              => 'nullable|array|min:3',
            'pictures.*'            => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048',
            'videos'                => 'nullable|array|min:1',
            'videos.*'              => 'nullable|file|mimes:mp4,avi|max:10240'
        ];
        
    }
}
