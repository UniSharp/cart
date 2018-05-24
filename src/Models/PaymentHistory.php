<?php
namespace UniSharp\PaymentHistory\Models;

use UniSharp\PaymentHistory\PaymentHistoryManager;
use UniSharp\PaymentHistory\Models\PaymentHistoryItem;
use Illuminate\Database\Eloquent\Model;
use UniSharp\PaymentHistory\Contracts\PaymentHistoryContract;
use UniSharp\PaymentHistory\Contracts\PaymentHistoryItemContract;

class PaymentHistory extends Model implements PaymentHistoryContract
{
    public function __construct(array $attributes = [])
    {
        $this->enums = [
            'payment' => get_class(resolve(PaymentContract::class)),
        ];
    }
}
