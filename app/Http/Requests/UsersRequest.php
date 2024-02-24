<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class UsersRequest extends FormRequest{
        public function authorize(){
            return true;
        }

        public function rules(){
            if($this->method() == 'PATCH'){
                return [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,'.$this->id
                ];
            }else{
                return [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email'
                ];
            }
        }

        public function messages(){
            return [
                'name.required' => 'Please enter name',
                'email.required' => 'Please enter email address',
                'email.email' => 'Please enter valid email address',
                'email.unique' => 'Email address already registered, Please use another email addresss'
            ];
        }
    }
