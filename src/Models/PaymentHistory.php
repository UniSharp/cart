<?php
namespace UniSharp\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Konekt\Enum\Eloquent\CastsEnums;
use UniSharp\Cart\PaymentHistoryManager;
use UniSharp\Cart\Contracts\PaymentContract;
use UniSharp\Cart\Contracts\PaymentHistoryContract;

class PaymentHistory extends Model implements PaymentHistoryContract
{
    use CastsEnums;
    protected $fillable = ['payment', 'price', 'comment'];
    protected $enums = [];
    public function __construct(array $attributes = [])
    {
        $this->enums = [
            'payment' => get_class(resolve(PaymentContract::class)),
        ];

        return parent::__construct($attributes);
    }
}
