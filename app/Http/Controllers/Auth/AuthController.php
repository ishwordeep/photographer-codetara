<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Jobs\SendEmailVerificationJob;
use App\Jobs\SendPasswordResetLinkJob;
use App\Mail\Auth\PasswordForgotMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Get the authenticated user (if any)
        $authUser = $request->user();


        // Check if the role is being set to admin or staff, and verify if the authenticated user is superadmin

        if (!$authUser || $authUser->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Only superadmin can register admin user'
            ], 403); // Forbidden response code
        }



        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        // Set role to customer by default if no role is specified
        // $role = $request->role ?? 'customer';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'admin',
            'is_active' => true,
        ]);


        SendEmailVerificationJob::dispatch($user);
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required ',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        $user = User::where('email', $request->email)->where('is_active',true)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }


        $token = $user->createToken('auth_token')->plainTextToken;
        if ($user->email_verified_at == null) {
            return response()->json([
                'status' => false,
                'message' => 'Email address is not verified. Please verify your email address.',
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => 'User logged in successfully',
            'data' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'User logged out successfully',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        // Validate the email input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Password reset failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Find the user by their email
        $user = User::where('email', $request->email)->first();

        // Generate the password reset token
        $token = app('auth.password.broker')->createToken($user);

        SendPasswordResetLinkJob::dispatch($user, $token);

        return response()->json([
            'status' => true,
            'message' => 'Password reset link sent successfully',
        ], Response::HTTP_OK);
    }

    public function resetPassword(Request $request)
    {
        // Validate the form data
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6', // confirmed means password_confirmation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Password reset failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Attempt to reset the password using Laravel's password broker
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        // Handle the response from the password broker
        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'status' => true,
                'message' => 'Password reset successfully',
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => false,
            'message' => 'Password reset failed',
            'errors' => ['email' => [__($status)]],
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
