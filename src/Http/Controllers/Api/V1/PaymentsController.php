<?php
namespace UniSharp\Cart\Http\Controllers\Api\V1;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\Enums\Payment;
use Illuminate\Routing\Controller;
use UniSharp\Cart\Contracts\OrderContract;

class PaymentsController extends Controller
{
    public function index()
    {
        return Payment::choices();
    }
}
