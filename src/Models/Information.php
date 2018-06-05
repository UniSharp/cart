<?php
namespace UniSharp\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Information extends Model
{
    use SoftDeletes;
    protected $fillable = ['type', 'name', 'address', 'email', 'phone'];
}
