<?php
namespace UniSharp\Cart\Models;

use UniSharp\Buyable\Models\Spec;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['id', 'cart_id', 'quentity'];
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
