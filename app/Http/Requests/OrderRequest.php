<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class OrderRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            if($this->method() == 'PATCH'){
                return [
                    'name' => 'required'
                ];
            }else{
                return [
                    'name' => 'required'
                ];
            }
        }

        public function messages(){
            return [
                'name.required' => 'Please enter name'
            ];
        }
    }
