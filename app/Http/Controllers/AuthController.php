<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use TymonJWTAuth\Exceptions\JWTException;
use TymonJWTAuth\ExceptionsToken\ExpiredException;
use TymonJWTAuth\Exceptions\TokenInvalidException;
use TymonJWTAuth\JWTAuth;
use App\User;
use Berkayk\OneSignal\OneSignalFacade as OneSignal;

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

    
    public function login(Request $request)
    {
        $response = ['error' => 'Forwarding issue'];

        $loginType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $loginType => $request->email,
            'password' => $request->password
        ];
        // $credentials = $request->only('email', 'password');
        
        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['status' => 'error', 'data' => 'Email/Password Salah!'],401);
        }

        // return response()->json(['status' => 'success', 'data' => Auth::user()],200);
        return $this->respondWithToken($token);
    }
    


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        // OneSignal::sendNotificationToUser(
        //     "Some Message", 
        //     $this->guard()->user()->token,
        //     $url = null, 
        //     $data = null, 
        //     $buttons = null, 
        //     $schedule = null
        // );
        return response()->json(['data' => $this->guard()->user()]);
    }

    /**
     * Get the authenticated User with role and permission.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json($this->guard()->user()->load(['roles','roles.permissions']));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     operationId="/api/v1/auth/logout",
     *     tags={"login"},
     *     @OA\Response(
     *         response="200",
     *         description="Returns message => Successfully logged out",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *     ),
     *     security = {{"bearerAuth":{}}}
     * )
     */
    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     /**
     * @OA\Post(
     *     path="/api/v1/auth/refresh",
     *     operationId="/api/v1/auth/refresh",
     *     tags={"login"},
     *     @OA\Response(
     *         response="200",
     *         description="Returns token",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *     ),
     *     security = {{"bearerAuth":{}}}
     * )
     */

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => $this->guard()->user(),
            'status' => 'success'
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }

    /**
     * Change Password
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'password_confirmation' => 'required|min:6',
            'password' => 'required|confirmed|min:6'
        ]);
         
        $user = $this->guard()->user();
        $user->password = app('hash')->make($request->get('password'));
        $user->save();

        Auth::logout();

    }
}    
