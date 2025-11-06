<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MonumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'author' => ['string', 'max:50'],
            'title' => ['required', 'string', 'max:100'],
            'place' => ['string', 'max:50'],
            'publ_date_from' => ['string', 'regex:/^\d{1,2}\.\d{4}$/'],
            'publ_date_to' => ['string', 'regex:/^\d{1,2}\.\d{4}$/'],
            'pages' => ['string', 'max:20'],
//            'pol' => ['required', 'in:f,m'],
//            'roles' => ['required', 'array', 'exists:roles,id'],
        ];
    }
    
    public function messages()
    {
        return [
            'title.required' => 'Введите название',
        ];
    }
    
    public function attributes()
    {
        return [
            'author' => trans('monument.author'),
            'title' => trans('corpus.name'),
            'place' => trans('monument.place'),
            'pages' => trans('monument.pages'),
        ];
    }
}
