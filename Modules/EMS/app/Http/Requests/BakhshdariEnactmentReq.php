<?php

namespace Modules\EMS\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BakhshdariEnactmentReq extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            "startDate" => ["required"],
            "endDate" => ["required"],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
