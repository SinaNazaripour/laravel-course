<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // protected $stopOnFirstFailure = true; #validation will stop with first failed field
    protected $redirect = "path to redirce after fail";
    protected $redirectRoute = "path to redirce after fail"; #or this

    public function authorize(): bool
    {
        // return false; 
        // return true; #to request be handeled we hav to treu this or make condition

        $condition = $this->route('optionalParameter') ? true : false; #use route parameters

        return  $condition;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "field_one" => "required|string|max:12",
            "field_two" => "required|string|size:3",
        ];
    }
}
