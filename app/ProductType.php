<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
	protected $table = "products_type";

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_type'
	];

	// public function product() {
	// 	return $this->belongsTo('App\Product');
	// }
}
