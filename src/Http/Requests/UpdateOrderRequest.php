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
            'receiver_information.phone' => 'string|nullable',
            'receiver_information.name' => 'string|nullable',
            'receiver_information.email' => 'email|nullable',
            'receiver_information.address' => 'string|nullable',
            'buyer_information.phone' => 'string|nullable',
            'buyer_information.name' => 'string|nullable',
            'buyer_information.email' => 'email|nullable',
            'buyer_information.address' => 'string|nullable',
        ];
    }
}
