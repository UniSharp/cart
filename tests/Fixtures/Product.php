<?php
namespace UniSharp\Cart\Tests\Fixtures;

use UniSharp\Buyable\Traits\Buyable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Buyable;
    protected $fillable = ['name'];
}
