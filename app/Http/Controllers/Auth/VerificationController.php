<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    // public function verify(EmailVerificationRequest $request)
    // {
    //     $request->fulfill();

    //     return response()->json(['message' => 'Email verified successfully.']);
    // }
    public function verify($id, $hash)
    {
        $user = User::find($id);
         // Check if the user exists and if the hash matches
         if (!$user || !hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json(['message' => 'Invalid verification link.'], 404);
        }

        // Check if the email is already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        // Mark the email as verified
        $user->markEmailAsVerified();

        return response()->json(['message' => 'Email verified successfully.'], 200);
    }


    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link resent.']);
    }
}
