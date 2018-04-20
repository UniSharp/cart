<?php
namespace UniSharp\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
{
    public function authorize()
    {
        return $this->cart->user_id ? $this->cart->user_id == auth()->user()->id : true;
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
