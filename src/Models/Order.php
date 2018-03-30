<?php
namespace UniSharp\Cart\Models;

use UniSharp\Cart\Models\OrderItem;
use Konekt\Enum\Eloquent\CastsEnums;
use UniSharp\Cart\Models\Information;
use Illuminate\Database\Eloquent\Model;
use UniSharp\Cart\Contracts\OrderContract;
use UniSharp\Cart\Contracts\OrderStatusContract;
use UniSharp\Cart\Contracts\ShippingStatusContract;

class Order extends Model implements OrderContract
{
    use CastsEnums;

    protected $enums = [
    ];
    public function __construct()
    {
        $this->enums = [
            'status' => get_class(resolve(OrderStatusContract::class)),
            'shipping_status' => get_class(resolve(ShippingStatusContract::class)),
        ];
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function buyerInformation()
    {
        return $this->hasOne(Information::class)->where('type', 'buyer');
    }

    public function receiverInformation()
    {
        return $this->hasOne(Information::class)->where('type', 'receiver');
    }
}
