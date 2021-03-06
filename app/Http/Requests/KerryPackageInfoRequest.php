<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KerryPackageInfoRequest extends FormRequest
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
            'tracking_id' => 'required|integer',
            'customer_no' => 'required|integer',
            'tracking_number' => 'required|integer'
        ];
    }
}
