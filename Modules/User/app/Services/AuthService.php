<?php

namespace Modules\User\App\Services;

use Modules\User\Models\User;
use App\Models\OtpAuthenticate;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthService
{
    use ApiResponseTrait;

    public function login($request)
    {
        $loginuserData = $request->validated();
        $user = User::where('email', $loginuserData['email'])->first();

        if (! $user || ! Hash::check($loginuserData['password'], $user->password)) {
            return $this->errorUnauthorized('Invalid Credentials');
        }
        if ($user->is_Active == 0) {
            return $this->errorUnauthorized('Invalid Credentials');
        }
    }

    public function refreshToken($request)
    {
        $user = auth('api')->user();

        if (! $request->user()->currentAccessToken()->can('refresh')) {
            return response()->json(['message' => 'Invalid refresh token'], 403);
        }
        $user->tokens()->delete();

    }

    public function verifyOtp(string $email, string $otp): void
    {
        $otpRecord = OtpAuthenticate::where('email', $email)
            ->latest()
            ->first();

        if (! $otpRecord) {
            throw ValidationException::withMessages([
                'otp' => 'No OTP found.',
            ]);
        }

        if (now()->greaterThan($otpRecord->expiryDate)) {
            throw ValidationException::withMessages([
                'otp' => 'The OTP has expired.',
            ]);
        }

        if ($otp !== $otpRecord->otp) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid OTP.',
            ]);
        }

        $user = User::where('email', $email)->firstOrFail();

        $user->update([
            'email_verified_at' => now(),
        ]);

        $otpRecord->delete();

        // Optional
        // $this->refreshToken($user);
    }


    public function resendOtp($request)
    {
        $dataRequest = $request->validate(['email' => 'required', 'exists:users,email']);
        $otpRecord = OtpAuthenticate::create([
            'email' => $dataRequest['email'],
            'otp' => mt_rand(100000, 999999),
            'expiryDate' => now()->addMinutes(15),
        ]);
        $user = User::where('email', $dataRequest['email'])->first();
        // Mail::to($dataRequest['email'])->send(new OtpMail($otpRecord['otp'], $recipientName));

        return $this->respondWithSuccess('The Otp Resend Successfully',
            [
                'test-otp-only' => $otpRecord['otp'],
            ]);
    }

    public function forgetPassword(string $email): array
    {
        OtpAuthenticate::where('email', $email)->delete();

        $otp = mt_rand(100000, 999999);

        OtpAuthenticate::create([
            'email' => $email,
            'otp' => $otp,
            'expiryDate' => now()->addMinutes(15),
        ]);

        $user = User::where('email', $email)->first();

        // هنا فقط Logic
        // event(new SendOtpEvent($email, $otp));

        return [
            'otp' => $otp,
            'user_exists' => (bool) $user,
        ];
    }

    public function resetPassword($request)
    {
        $dataRequest = $request->validated();
        $user = $request->user();
        $user->password = Hash::make($dataRequest['password']);
        $user->save();
        OtpAuthenticate::where('email', $user->email)->delete();
        $user->tokens()->delete();

        return $this->respondWithSuccess('Password Has Changed Successfully');
    }

    public function me()
    {
        return auth('api')->user();
    }

    public function updatePassword($request, $user)
    {
        $requestPasswordValidated = $request->validated();
        if ($user instanceof User) {
            $user->update(['password' => Hash::make($requestPasswordValidated['password'])]);

            return $this->respondWithSuccess('Password Updated Successfully');
        }
        throw new \Exception('User Not Found Currently!');
    }
}
