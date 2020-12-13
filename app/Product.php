<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	protected $table = "products";
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_type', 'name', 'short_description', 'description', 'category_id', 'price', 'in_stock', 'quantity', 'product_images', 'child_products', 'related_products'
	];

	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
	protected $casts = [
		'product_images' => 'array',
		'child_products' => 'array',
		'related_products' => 'array'
	];

	// public function product_type() {
	// 	return $this->hasOne('App\ProductType');
	// }

	// public function category() {
	// 	return $this->hasOne('App\Category');
	// }
}
