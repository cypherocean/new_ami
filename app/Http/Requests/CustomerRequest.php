<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class CustomerRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            if($this->method() == 'PATCH'){
                return [
                    'party_name' => 'required|unique:customers,party_name,'.$this->id
                ];
            }else{
                return [
                    'party_name' => 'required|unique:customers,party_name'
                ];
            }
        }

        public function messages(){
            return [
                'party_name.required' => 'Please enter party name',
                'party_name.unique' => 'Party name is already exists, please use another one'
            ];
        }
    }
