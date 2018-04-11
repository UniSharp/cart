<?php
namespace UniSharp\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefreshCartRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'specs' => 'array',
            'specs.*.id' => 'exists:specs,id',
            'specs.*.quantity' => 'numeric',
        ];
    }
}
