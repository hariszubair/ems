<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StoreLeaveRequest extends FormRequest
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
            'from' => ['required', 'date', 'before_or_equal:to', 'after_or_equal:' . Carbon::now()->format('Y-m-d')],
            'to' => ['required', 'date', 'after_or_equal:from', 'after_or_equal:' . Carbon::now()->format('Y-m-d')],
            'type' => ['required', Rule::in(['Casual', 'Annual', 'Unpaid'])],
        ];
    }
}
