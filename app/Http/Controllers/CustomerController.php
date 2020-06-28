<?php
/**
 * CustomerController
 * php version 7.3.17
 *
 * @category CustomerController
 * @package  E-commerce
 * @author   Nalin Vaidya <nalinvaidya@gmail.com>
 * @license  https://github.com/nalinv123/ecommerce-api None
 * @link     https://github.com/nalinv123/ecommerce-api
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\User as Customer;
use Validator;

/**
 * CustomerController
 * 
 * @category CustomerController
 * @package  E-commerce
 * @author   Nalin Vaidya <nalinvaidya@gmail.com>
 * @license  https://github.com/nalinv123/ecommerce-api None
 * @link     https://github.com/nalinv123/ecommerce-api
 */
class CustomerController extends Controller
{
    /**
     * Create an customer.
     *
     * @param Request $request Request object containing user input.
     * 
     * @return void
     */
    public function create(Request $request)
    {
        
        Log::info(
            "Create customer request with ", 
            array('request' => $request->all())
        );

        $validator = Validator::make(
            $request->all(), [
            'username' => 'required|string',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|email|unique:user',
            'password' => 'required|string|confirmed',
            'user_role_id' => 'required|integer'
            ]
        );
        
        if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
        }
        
        $user = new Customer(
            [
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'user_role_id' => $request->user_role_id
            ]
        );

        $user->save();

        return $this->sendResponse("Success", "Customer successfully created.");
    }

    /**
     * Authenticate an customer.
     *
     * @param Request $request Request object containing user input.
     * 
     * @return void
     */
    public function authenticate(Request $request)
    {
        Log::info(
            "Customer authenticate request with ",
            array('request' => $request->all())
        );

        $validator = Validator::make(
            $request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
            ]
        );

        if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
        }

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return $this->sendError("Unauthorized user", [], 401);
        }

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        $response = [
        'access_token' => $tokenResult->accessToken,
        'token_type' => 'Bearer',
        'expires_at' => Carbon::parse(
            $tokenResult->token->expires_at
        )->toDateTimeString()
        ];

        return $this->sendResponse($response, "Customer authenticated");
    }
    
    /**
     * Logout customer.
     *
     * @param Request $request Request object containing user input.
     * 
     * @return void
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->sendResponse("", "User successfully logged out.");
    }
    
    /**
     * Fetch all customers.
     *
     * @return void
     */
    public function getAll()
    {
        $response = Customer::where('user_role_id', 2)->get();
        return $this->sendResponse($response->toArray(), "");
    }
    
    /**
     * Fetch specific customer by Id.
     *
     * @param int $id Customer Id.
     * 
     * @return void
     */
    public function get($id)
    {
        $response = Customer::find($id);
        if ($response->user_role_id === 2) {
            return $this->sendResponse($response->toArray(), "");
        } else {
            return $this->sendError("Not found");
        }
    }
    
    /**
     * Update / Edit Customer
     *
     * @param Request $request Request object containing user input.
     * 
     * @return void
     */
    public function edit(Request $request)
    {
        $validator = Validator::make(
            $request->all(), [
                'id' => 'required|integer',
                'username' => 'required|string',
                'firstname' => 'required|string',
                'lastname' => 'required|string',
            ]
        );

        if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
        }
        
        $customer = Customer::find($request->id);
        if ($customer->user_role_id === 2) {
            $customer->username = $request->username;
            $customer->firstname = $request->firstname;
            $customer->lastname = $request->lastname;

            $customer->save();
            return $this->sendResponse($customer, "Customer updated successfully");
        } else {
            return $this->sendError("Not found");
        }
    }
    
    /**
     * Removes specific customer by Id.
     *
     * @param int $id Customer id.
     * 
     * @return void
     */
    public function remove($id)
    {
        if (Auth::user()->id === $id) {
            return $this->sendError(
                "You cannot delete currently logged in administrator",
                [],
                403
            );
        } else {
            $customer = Customer::find($id);
            if ($customer) {
                $customer->delete();
                return $this->sendResponse("", "Customer deleted");
            } else {
                return $this->sendError("Not found");
            }
        }
    }
}
