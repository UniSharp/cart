<?php
namespace UniSharp\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Konekt\Enum\Eloquent\CastsEnums;
use UniSharp\Buyable\Models\Spec;
use UniSharp\Cart\Contracts\OrderItemContract;
use UniSharp\Cart\Contracts\OrderItemStatusContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model implements OrderItemContract
{
    use CastsEnums;
    use SoftDeletes;

    protected $fillable = ['id', 'status', 'name', 'spec', 'sku', 'price', 'order_id', 'quantity', 'spec_id'];
    protected $enums = [];

    public function __construct(array $attributes = [])
    {
        $this->enums = [
            'status' => get_class(resolve(OrderItemStatusContract::class)),
        ];
        return parent::__construct($attributes);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function spec()
    {
        return $this->belongsTo(Spec::class);
    }
}
