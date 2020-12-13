<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	protected $table = "product_categories";
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'display_at_top', 'parent_category'
	];

	// public function product() {
	// 	return $this->belongsTo('App\Product');
	// }
}
