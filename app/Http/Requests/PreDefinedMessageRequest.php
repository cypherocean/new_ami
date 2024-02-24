<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreDefinedMessageRequest extends FormRequest
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
        return [
            'message' => 'required'
        ];
    }

    public function messages(){
        return [
            'message.required' => 'Please enter message',
        ];
    }
}
