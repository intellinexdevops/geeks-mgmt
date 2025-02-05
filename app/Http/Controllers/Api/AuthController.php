<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('api')->user();
        $user['access_token'] = $token;
        $user['avatar'] = env('APP_URL') . Storage::url($user->avatar);
        $user['expires_in'] = auth('api')->factory()->getTTL();

        return ResponseHelper::success(
            $user,
            "Login Successfully",
            200
        );
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {

        $user = auth('api')->user();

        $user->avatar = env('APP_URL') . Storage::url($user->avatar);

        return ResponseHelper::success(
            $user,
            "Successfully",
            200
        );
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return ResponseHelper::success(
            null,
            "Logout successfully.",
            200
        );
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $refreshToken = auth('api')->refresh();
        return ResponseHelper::success(
            $refreshToken,
            "Successfully refreshed token",
            200
        );
    }

    public function update(Request $request)
    {
        $user = auth('api')->user();

        $data = [];
        if (isset($request->username)) {
            $data["username"] = $request->username;
        }
        if (isset($request->name)) {
            $data["name"] = $request->name;
        }
        if ($request->hasFile('avatar')) {
            // Validate the avatar file
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // max size 2MB
            ]);

            // Store the avatar file
            $avatarPath = $request->file('avatar')->store('uploads', 'public');

            // Save the file path to the database
            $data["avatar"] = $avatarPath;
        }
        if (isset($request->mobile)) {
            $data["mobile"] = $request->mobile;
        }
        if (isset($request->location_id)) {
            $data["location_id"] = $request->location_id;
        }
        if (isset($request->job_title)) {
            $data["job_title"] = $request->job_title;
        }

        User::where('id', $user->id)->update($data);
        $newUserUpdated = User::where('id', $user->id)->first();
        $newUserUpdated->avatar = env('APP_URL') . Storage::url($user->newUserUpdated);


        return ResponseHelper::success(
            $newUserUpdated,
            "Successfully updated.",
            201,
        );
    }
}