<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'User details fetched successfully',
            'user' => new UserResource($request->user())
        ], Response::HTTP_OK);
    }

    public function profileUpdate(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' =>  $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {

            $user->update($request->only([
                'name',
                'phone'
            ]));
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => new UserResource($user)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed',
                'errors' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed', // confirmed means password_confirmation field is required
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Password change failed',
                'errors' =>  $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

         // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password change failed',
                'errors' => ['current_password' => ['Current password is incorrect']]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check if the new password is the same as the current password
        if ($request->current_password == $request->password) {
            return response()->json([
                'success' => false,
                'message' => 'Password change failed',
                'errors' => ['password' => ['New password cannot be the same as current password']]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Change the password
        try {
            $user->update([
                'password' => bcrypt($request->password)
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password change failed',
                'errors' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateProfilePicture(Request $request)
    {
        // dd($request->hasFile('image'));
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Profile picture update failed',
                'errors' =>  $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            if ($request->hasFile('image')) {
                $data['image'] = storeImage($request->file('image'), 'users');
            }else{
                $data['image'] = null;
            }
           
            $user->update([
                'image' => $data['image']
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'user' => new UserResource($user)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile picture update failed',
                'errors' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
}
