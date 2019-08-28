<?php

namespace Modules\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'full_name' => 'required',
            'username' => 'required|min:6|unique:logins,username,' . $_REQUEST['login_id'],
            'password' => 'required|min:8',
            're_password' => 'required|same:password',
            'email' => 'required|email|unique:logins,email,' . $_REQUEST['login_id'],
            'phone' => 'required|unique:people,phone,' . $_REQUEST['person_id'],  
            'role_id' => 'required',
            'branch_id' => 'required'
        ];
    }

     /**
     * Listener on validation fails.
     *
     * @return array
     */
    // protected function failedValidation(Validator $validator)
    // {
    //     dd($_REQUEST['login_id']);
    //     throw new HttpResponseException(response()->json($validator->errors()));

    // }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}