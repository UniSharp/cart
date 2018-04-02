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
