<?php
namespace UniSharp\Cart\Model;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['id', 'cart_id', 'quentity'];
}
