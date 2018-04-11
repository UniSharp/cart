<?php
namespace UniSharp\Cart\Models;

use UniSharp\Buyable\Models\Spec;
use Illuminate\Database\Eloquent\Model;
use UniSharp\Cart\Contracts\CartItemContract;

class CartItem extends Model implements CartItemContract
{
    protected $fillable = ['id', 'cart_id', 'quantity'];
    protected $appends = ['price'];

    public function spec()
    {
        return $this->belongsTo(Spec::class, 'id');
    }

    public function getPriceAttribute()
    {
        return $this->spec->price;
    }
}
