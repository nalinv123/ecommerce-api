<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User as Admin;
use Validator;

class AdminController extends Controller
{
    public function create(Request $request)
    {

        Log::info("Create admin user request with ", array('request' => $request->all()));

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
            return $this->sendError("Validation error.", $validator->errors());
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

    public function authenticate(Request $request)
    {

        Log::info("Admin user authenticate request with ", array('request' => $request->all()));

        $validator = Validator::make(
            $request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
            ]
        );

        if ($validator->fails()) {
            return $this->sendError("Validation error.", $validator->errors());
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

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->sendResponse("", "User successfully logged out.");
    }

    public function getAll()
    {
        $response = Admin::all();
        return $this->sendResponse($response->toArray(), "");
    }

    public function get($id)
    {
        $response = Admin::find($id);
        if ($response) {
            return $this->sendResponse($response->toArray(), "");
        } else {
            return $this->sendResponse([], "Not found");
        }
    }

    public function edit(Request $request)
    {

    }

    public function remove(Request $request)
    {

    }
}
