<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Category;
use Validator;

class CategoryController extends Controller
{
    public function add(Request $request) {
		Log::info(
            "Create product category request with ", 
            array('request' => $request->all())
		);

		$validator = Validator::make(
            $request->all(), [
            'name' => 'required|string|unique:product_categories',
            'description' => 'required|string',
            'display_at_top' => 'required|integer',
            'parent_category' => 'required|integer'
            ]
		);
		
		if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
		}

		$category = new Category($request->all());

		$category->save();

        return $this->sendResponse("Success", "Category successfully created.");
	}

	public function get($id) {
		$category = Category::find($id);

		if ($category) {
			return $this->sendResponse($category->toArray(), "");
		} else {
			return $this->sendError("Not found");
		}
	}

	public function getAll() {
		$categories = Category::all();
		return $this->sendResponse($categories->toArray(), "");
	}

	public function edit(Request $request)
	{
		Log::info(
            "Update product category request with ", 
            array('request' => $request->all())
		);

		$validator = Validator::make(
            $request->all(), [
			'id' => 'required|integer',
            'name' => 'required|string|unique:product_categories',
            'description' => 'required|string',
            'display_at_top' => 'required|integer',
            'parent_category' => 'required|integer'
            ]
		);
		
		if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
		}

		$category = Category::find($request->id);
		if ($category) {
			$category->name = $request->name;
			$category->description = $request->description;
			$category->display_at_top = $request->display_at_top;
			$category->parent_category = $request->parent_category;

			$category->save();
			return $this->sendResponse($category, "Category updated successfully");
		} else {
			return $this->sendError("Not found");
		}
	}

	public function remove(Request $request)
	{
		Log::info(
            "Remove product category request with ", 
            array('request' => $request->all())
		);

		$validator = Validator::make(
            $request->all(), [
			'id' => 'required|integer',
            ]
		);
		
		if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
		}

		$category = Category::find($request->id);
		if ($category) {
			$category->delete();
			return $this->sendResponse("", "Category deleted successfully");
		} else {
			return $this->sendError("Not found");
		}
	}
}
