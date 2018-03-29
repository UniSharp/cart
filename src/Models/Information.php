<?php
namespace UniSharp\Cart\Models;

use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    protected $fillable = ['type', 'name', 'address', 'email', 'phone'];
}
