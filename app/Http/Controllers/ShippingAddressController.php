<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\User as Customer;
use App\ShippingAddress;
use Validator;

class ShippingAddressController extends Controller
{
    public function add(Request $request) {
		
		Log::info(
            "Add customer shipping address request with ", 
            array('request' => $request->all())
		);
		
		$validator = Validator::make(
            $request->all(), [
            'address_line_one' => 'required|string',
            'address_line_two' => 'string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
			'pincode' => 'required|integer',
			'user_id' => 'required|integer'
            ]
		);
		
		if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
		}
		
		$customer = Customer::find($request->user_id);
		if ($customer && $customer->user_role_id == 2) {
			$shippingAddress = new ShippingAddress([
				'address_line_one' => $request->address_line_one,
				'address_line_two' => $request->address_line_two,
				'city' => $request->city,
				'state' => $request->state,
				'country' => $request->country,
				'pincode' => $request->pincode,
				]
			);

			$customer->shipping_address()->save($shippingAddress);

			return $this->sendResponse("Success", "Customer shipping address successfully saved.");
		} else {
			return $this->sendError("Not found");
		}
	}

	public function getAll($user_id) {
		$response = Customer::find($user_id);
		if ($response && $response->shipping_address) {
			return $this->sendResponse($response->shipping_address->toArray(), "");
		}
		else {
			return $this->sendError("Not found");
		}
	}

	public function edit(Request $request) {
		Log::info(
            "Update customer shipping address request with ", 
            array('request' => $request->all())
		);
		
		$validator = Validator::make(
            $request->all(), [
            'address_line_one' => 'required|string',
            'address_line_two' => 'string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
			'pincode' => 'required|integer',
			'user_id' => 'required|integer',
			'shipping_address_id' => 'required|integer'
            ]
		);

		if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
		}

		$shippingAddress = ShippingAddress::find($request->shipping_address_id);
		if ($shippingAddress && $shippingAddress->user_id == $request->user_id) {
			$shippingAddress->address_line_one = $request->address_line_one;
			$shippingAddress->address_line_two = $request->address_line_two;
			$shippingAddress->city = $request->city;
			$shippingAddress->state = $request->state;
			$shippingAddress->country = $request->country;
			$shippingAddress->pincode = $request->pincode;

			$shippingAddress->save();
			return $this->sendResponse($shippingAddress, "Customer shipping address updated successfully");
		} else {
			return $this->sendError("You are trying to edit the shipping address of other user", "", 422);
		}
	}

	public function remove(Request $request)
	{
		Log::info(
            "Delete customer shipping address request with ", 
            array('request' => $request->all())
		);

		$validator = Validator::make(
            $request->all(), [
			'user_id' => 'required|integer',
			'shipping_address_id' => 'required|integer'
            ]
		);

		if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
		}

		$shippingAddress = ShippingAddress::find($request->shipping_address_id);
		if ($shippingAddress && $shippingAddress->user_id == $request->user_id) {
			$shippingAddress->delete();
			return $this->sendResponse("", "Customer shipping address deleted successfully");
		} else {
			return $this->sendError("You are trying to delete the shipping address of other user", "", 422);
		}
	}
}
