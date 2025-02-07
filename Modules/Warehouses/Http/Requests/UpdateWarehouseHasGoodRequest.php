<?php

namespace Modules\Warehouses\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseHasGoodRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'amount' => 'required|numeric',
          'vendor_code' => 'required'
        ];
    }

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
