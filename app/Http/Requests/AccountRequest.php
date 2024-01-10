<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
            'errors'=> $validator->errors(),
            'code'=>422
        ]));
    }

    public function rules(): array
    {
        return [
            "firstname"=>"required|max:255",
            "lastname"=>"required|max:255",
            "company"=>"required",
            "phonenumber"=>"required",
            "email"=>"required|email|unique:users",
            "city"=>"required",
            "password"=>"required"
        ];
    }

    public function messages(){
        return [
            "firstname.required" => "Please enter your first name.",
            "lastname.required" => "Please enter your last name.",
            "company.required" => "Please enter your company name.",
            "phonenumber.required" => "Please enter your phone number.",
            "email.required" => "Please enter your email.",
            "city.required" => "Please enter your city.",
            "password.required" => "Password must not empty.",
        ];
    }
}
