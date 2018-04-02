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
            'receiver_information.phone' => 'string',
            'receiver_information.name' => 'string',
            'receiver_information.email' => 'email',
            'receiver_information.address' => 'string',
            'buyer_information.phone' => 'string',
            'buyer_information.name' => 'string',
            'buyer_information.email' => 'email',
            'buyer_information.address' => 'string',
        ];
    }
}
