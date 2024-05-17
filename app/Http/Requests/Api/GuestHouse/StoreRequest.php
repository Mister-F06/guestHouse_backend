<?php

namespace App\Http\Requests\Api\GuestHouse;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name'          => 'required|string|unique:guest_houses,name|max:255',
            'description'   => 'required|string',
            'price'         => 'required|integer',
            'address'       => 'required|string',
            'map_link'      => 'nullable|string',
            'bedrooms_nbr'  => 'required|integer',
            'beds_nbr'      => 'required|integer',
            'toilets_nbr'   => 'required|integer',
            'bathrooms_nbr' => 'required|integer',
            'has_kitchen'   => 'required|boolean',
            'has_pool'      => 'required|boolean',
            'has_air_conditionner' => 'required|boolean',
            'has_jacuzzi'   => 'required|boolean',
            'has_washing_machine' => 'required|boolean',
            'has_car'       => 'required|boolean',
            'has_parking'   => 'required|boolean',
            'cover'         => 'required|file|mimes:png,jpg,jpeg,webp|max:5120',
            'pictures'      => 'array|min:3',
            'pictures.*'    => 'file|mimes:png,jpg,jpeg,webp|max:5120',
            'videos'        => 'array|min:1',
            'videos.*'      => 'file|mimes:mp4,avi|max:10240'
        ];
    }
}
