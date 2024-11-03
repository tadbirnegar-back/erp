<?php

namespace Modules\AAA\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePassUserReq extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'newPassword' => ['required']
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
