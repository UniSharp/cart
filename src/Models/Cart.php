<?php
namespace UniSharp\Cart\Models;

use UniSharp\Cart\CartManager;
use UniSharp\Cart\Models\CartItem;
use Illuminate\Database\Eloquent\Model;
use UniSharp\Cart\Contracts\CartContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use UniSharp\Cart\Contracts\CartItemContract;
use Askedio\SoftCascade\Traits\SoftCascadeTrait;

class Cart extends Model implements CartContract
{
    use SoftDeletes;
    use SoftCascadeTrait;

    public $incrementing = false;
    protected $fillable = ['id'];
    protected $softCascade = ['items'];
    protected $appends = [
        'price', 'fee', 'originalPrice'
    ];

    public function items()
    {
        return $this->hasMany(get_class(resolve(CartItemContract::class)));
    }

    public function getPriceAttribute()
    {
        return CartManager::make($this)->getPrice();
    }

    public function getOriginalPriceAttribute()
    {
        return CartManager::make($this)->getOriginalPrice();
    }

    public function getFeeAttribute()
    {
        return CartManager::make($this)->getFee();
    }
}
