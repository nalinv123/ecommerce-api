<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\User as Customer;
use App\BillingAddress;
use Validator;

class BillingAddressController extends Controller
{
    public function add(Request $request) {
		
		Log::info(
            "Add customer billing address request with ", 
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
			$billingAddress = new BillingAddress([
				'address_line_one' => $request->address_line_one,
				'address_line_two' => $request->address_line_two,
				'city' => $request->city,
				'state' => $request->state,
				'country' => $request->country,
				'pincode' => $request->pincode,
				]
			);

			$customer->billing_address()->save($billingAddress);

			return $this->sendResponse("Success", "Customer billing address successfully saved.");
		} else {
			return $this->sendError("Not found");
		}
	}

	public function getAll($user_id) {
		$response = Customer::find($user_id); /* BillingAddress::where('user_id', $user_id)->get(); */
		if ($response && $response->billing_address) {
			return $this->sendResponse($response->billing_address->toArray(), "");
		}
		else {
			return $this->sendError("Not found");
		}
	}

	public function edit(Request $request) {
		Log::info(
            "Update customer billing address request with ", 
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
			'billing_address_id' => 'required|integer'
            ]
		);

		if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
		}

		$billingAddress = BillingAddress::find($request->billing_address_id);
		if ($billingAddress && $billingAddress->user_id == $request->user_id) {
			$billingAddress->address_line_one = $request->address_line_one;
			$billingAddress->address_line_two = $request->address_line_two;
			$billingAddress->city = $request->city;
			$billingAddress->state = $request->state;
			$billingAddress->country = $request->country;
			$billingAddress->pincode = $request->pincode;

			$billingAddress->save();
			return $this->sendResponse($billingAddress, "Customer biiling address updated successfully");
		} else {
			return $this->sendError("You are trying to edit the billing address of other user", "", 422);
		}
	}

	public function remove(Request $request)
	{
		Log::info(
            "Delete customer billing address request with ", 
            array('request' => $request->all())
		);

		$validator = Validator::make(
            $request->all(), [
			'user_id' => 'required|integer',
			'billing_address_id' => 'required|integer'
            ]
		);

		if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
		}

		$billingAddress = BillingAddress::find($request->billing_address_id);
		if ($billingAddress && $billingAddress->user_id == $request->user_id) {
			$billingAddress->delete();
			return $this->sendResponse("", "Customer biiling address deleted successfully");
		} else {
			return $this->sendError("You are trying to delete the billing address of other user", "", 422);
		}
	}
}
