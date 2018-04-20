<?php
namespace UniSharp\Cart\Http\Controllers\Api\V1;

use UniSharp\Cart\CartManager;
use Illuminate\Routing\Controller;
use UniSharp\Cart\Contracts\OrderContract;

class PaymentsController extends Controller
{
    public function pay(OrderContract $order)
    {

    }
}
