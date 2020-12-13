<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Product;
use App\Category;
use App\ProductType;
use Validator;
use Image;

class ProductController extends Controller
{
    public function add(Request $request) {
		Log::info(
            "Create product request with ", 
            array('request' => $request->all())
		);

		$validator = Validator::make(
            $request->all(), [
			'product_type' => 'required|int',
            'name' => 'required|string|unique:products',
            'short_description' => 'required|string',
            'description' => 'required|string',
            'category_id' => 'required|integer',
			'price' => 'required|integer',
			'in_stock' => 'required|boolean',
			'quantity' => 'required|integer',
			'product_images' => 'required',
			'product_images.*' => 'image|mimes:png,jpg,jpeg|max:1000',
			'child_products' => 'required',
			'related_products' => 'required',
            ]
		);
		
		if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
		}

		$userInput = $request->all();

		$category = Category::find($userInput['category_id']);
		if (!$category) {
			return $this->sendError("Category Not found");
		}
		$product_type = ProductType::find($userInput['product_type']);
		if (!$product_type) {
			return $this->sendError("Product Type Not found");
		}

		$product_image_names = [];
		if ($request->hasFile('product_images')) {
			foreach ($request->file('product_images') as $index=>$product_image) {
				if ($product_image->isValid()) {
					$product_image_names[$index] = Str::slug($userInput['name'], "-") . "-" . $index . '.' . $product_image->getClientOriginalExtension();
					$large_image_path=public_path('product_images/large/'.$product_image_names[$index]);
                	$medium_image_path=public_path('product_images/medium/'.$product_image_names[$index]);
					$small_image_path=public_path('product_images/small/'.$product_image_names[$index]);

					//Resize Image
					Image::make($product_image)->save($large_image_path);
					Image::make($product_image)->resize(600,600)->save($medium_image_path);
					Image::make($product_image)->resize(300,300)->save($small_image_path);
				}
			}
		}
		$userInput['product_images'] = $product_image_names;
		// print_r($userInput);

		$product = new Product($userInput);
		$product->save();

		return $this->sendResponse("Success", "Product successfully created.");
	}
}
