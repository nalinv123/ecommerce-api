<?php
/**
 * AdminController
 * php version 7.3.17
 *
 * @category AdminController
 * @package  E-commerce
 * @author   Nalin Vaidya <nalinvaidya@gmail.com>
 * @license  https://github.com/nalinv123/ecommerce-api None
 * @link     https://github.com/nalinv123/ecommerce-api
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User as Admin;
use Validator;

/**
 * AdminController
 * 
 * @category AdminController
 * @package  E-commerce
 * @author   Nalin Vaidya <nalinvaidya@gmail.com>
 * @license  https://github.com/nalinv123/ecommerce-api None
 * @link     https://github.com/nalinv123/ecommerce-api
 */
class AdminController extends Controller
{
    
    /**
     * Create an admin user.
     *
     * @param Request $request Request object containing user input.
     * 
     * @return void
     */
    public function create(Request $request)
    {

        Log::info(
            "Create admin user request with ", 
            array('request' => $request->all())
        );

        $validator = Validator::make(
            $request->all(), [
            'username' => 'required|string',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|email|unique:admin_user',
            'password' => 'required|string|confirmed'
            ]
        );

        if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
        }

        $admin_user = new Admin(
            [
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => bcrypt($request->password)
            ]
        );

        $admin_user->save();

        return $this->sendResponse("Success", "Admin user successfully created.");
    }
    
    /**
     * Authenticate an admin user.
     *
     * @param Request $request Request object containing user input.
     * 
     * @return void
     */
    public function authenticate(Request $request)
    {

        Log::info(
            "Admin user authenticate request with ",
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

        $admin_user = $request->user();

        $tokenResult = $admin_user->createToken('Personal Access Token');
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

        return $this->sendResponse($response, "User authenticated");
    }
    
    /**
     * Logout admin user.
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
     * Fetch all admin users.
     *
     * @return void
     */
    public function getAll()
    {
        $response = Admin::all();
        return $this->sendResponse($response->toArray(), "");
    }
    
    /**
     * Fetch specific admin user by Id.
     *
     * @param int $id Admin user Id.
     * 
     * @return void
     */
    public function get($id)
    {
        $response = Admin::find($id);
        if ($response) {
            return $this->sendResponse($response->toArray(), "");
        } else {
            return $this->sendError("Not found");
        }
    }
    
    /**
     * Update / Edit admin User
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
                'email' => 'required|string|email',
                'password' => 'required|string|confirmed'
            ]
        );

        if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors(), 422);
        }
        
        $admin = Admin::find($request->id);
        if ($admin) {
            $admin->username = $request->username;
            $admin->firstname = $request->firstname;
            $admin->lastname = $request->lastname;
            $admin->email = $request->email;

            $admin->save();
            return $this->sendResponse("", "Admin user updated successfully");
        } else {
            return $this->sendError("Not found");
        }
    }
    
    /**
     * Removes specific admin user by Id.
     *
     * @param int $id Admin user id.
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
            $admin = Admin::find($id);
            if ($admin) {
                $admin->delete();
                return $this->sendResponse("", "Admin user deleted");
            } else {
                return $this->sendError("Not found");
            }
        }
    }
}
