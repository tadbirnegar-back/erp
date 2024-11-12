<?php

namespace Modules\EMS\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMeetingDateReq extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'newDate' => 'required',
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
