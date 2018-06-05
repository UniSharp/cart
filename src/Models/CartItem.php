<?php
namespace UniSharp\Cart\Models;

use UniSharp\Buyable\Models\Spec;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use UniSharp\Cart\Contracts\CartItemContract;

class CartItem extends Model implements CartItemContract
{
    use SoftDeletes;

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
