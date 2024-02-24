<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalendarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        return true;
    }

    public function rules(){
        if($this->method() == 'PATCH'){
            return [
                'users' => 'required',
                'title' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'eventDescription' => 'required',
            ];
        }else{
            return [
                'users' => 'required',
                'title' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'eventDescription' => 'required',
            ];
        }
    }

    public function messages(){
        return [
            'users.required' => 'Please select user',
            'title.required' => 'Please enter title',
            'start_time.required' => 'Please select start time',
            'end_time.required' => 'Please select end time',
            'eventDescription.required' => 'Please enter event description',
        ];
    }
}
