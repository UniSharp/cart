<?php
namespace UniSharp\Cart\Model;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['id', 'cart_id', 'quentity'];
}
