<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
    	'stripe_id',
    	'price',
    	'active'
    ];
}
