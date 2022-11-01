<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // هل لازم يكون مسجل دخول ليتطبق او لاا
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "user_id"=>"required|exists:users,id",
            "amount"=>"required|min:1",
            "blood_type_id"=>"required|exists:blood_types,id",
            "verified"=>"nullable",
        ];
    }
}
