<?php
namespace UniSharp\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'items' => 'array',
            'informations' => 'required|array',
            'informations.receiver' => 'array',
            'informations.buyer' => 'array',
        ];
    }
}
