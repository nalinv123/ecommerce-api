<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingAddress extends Model
{
    protected $table = "user_billing_address";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address_line_one', 'address_line_two', 'city', 'state', 'country', 'pincode', 'user_id'
	];
	
	public function user() {
		return $this->belongsTo('App\User');
	}
}
