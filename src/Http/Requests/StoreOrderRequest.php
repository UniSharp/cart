<?php
namespace UniSharp\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cart' => 'exists:carts,id|required',
            'informations' => 'required|array',
            'informations.receiver' => 'array',
            'informations.buyer' => 'array',
        ];
    }
}
