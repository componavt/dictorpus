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
    
    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();
        $this->addCustomDateValidation($validator); 
        return $validator;
    }   
    
    /**
     * Добавляет кастомную валидацию: 
     * - "по" не может быть без "с",
     * - "по" не может быть раньше "с".
     */
    private function addCustomDateValidation($validator) {
        $validator->after(function ($validator) {
            $from = $this->input('publ_date_from');
            $to   = $this->input('publ_date_to');

            $fromDate = parse_date_mm_yyyy($from);
            $toDate   = parse_date_mm_yyyy($to);

            if ($to && !$from) {
                $validator->errors()->add('publ_date_to', 'Нельзя указать дату "по", если не указана дата "с".');
            }

            if ($fromDate && $toDate && $toDate->lt($fromDate)) {
                $validator->errors()->add('publ_date_to', 'Дата "по" не может быть раньше даты "с".');
            }
        });
    }
    
}
