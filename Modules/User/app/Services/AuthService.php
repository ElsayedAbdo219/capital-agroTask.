<?php
namespace Modules\User\App\Services;

use Modules\User\Models\User;
use App\Models\OtpAuthenticate;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

    public function verifyOtp($request)
    {
        $dataRequest = $request->validate([
            'email' => 'required|email:filter|exists:users,email',
            'otp' => 'required|digits:6',
        ]);

        $otpRecord = OtpAuthenticate::where('email', $dataRequest['email'])->latest()->first();

        if (! $otpRecord) {
            return $this->errorUnauthorized('No OTP found.');
        }

        if (now()->greaterThan($otpRecord->expiryDate)) {
            return $this->errorUnauthorized('The OTP has expired.');
        }

        if ($dataRequest['otp'] != $otpRecord->otp) {
            return $this->errorUnauthorized('Invalid OTP.');
        }
        $user = User::where('email', $otpRecord->email)->first();
        $user->email_verified_at = now();
        $user->save();
        $otpRecord->delete();

        return $this->respondWithSuccess('User verified successfully!');
    }

    public function verifyAccount($request)
    {
        $dataRequest = $request->validate([
            'email' => 'required|email:filter|exists:users,email',
            'otp' => 'required|digits:6',
        ]);

        $otpRecord = OtpAuthenticate::where('email', $dataRequest['email'])->latest()->first();

        if (! $otpRecord) {
            return $this->errorUnauthorized('No OTP found.');
        }

        if (now()->greaterThan($otpRecord->expiryDate)) {
            return $this->errorUnauthorized('The OTP has expired.');
        }

        if ($dataRequest['otp'] != $otpRecord->otp) {
            return $this->errorUnauthorized('Invalid OTP.');
        }
        $user = User::where('email', $otpRecord->email)->first();
        $user->email_verified_at = now();
        $user->save();
        $otpRecord->delete();
        $user->tokens()->delete();
        $accessToken = $user->createToken('access-token', ['*'], now()->addMinutes(60))->plainTextToken;
        $refreshToken = $user->createToken('refresh-token', ['refresh'], now()->addDays(7))->plainTextToken;

        return $this->respondWithSuccess('User verified successfully.', [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 60,
        ]);
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
    public function forgetPassword($request)
    {
        $dataRequest = $request->validate([
            'email' => 'required|email:filter|exists:users,email',
        ]);
        OtpAuthenticate::where('email', $dataRequest['email'])->delete();

        $otpRecord =  OtpAuthenticate::create([
            'email' => $dataRequest['email'],
            'otp' =>  mt_rand(100000, 999999),
            'expiryDate' => now()->addMinutes(15),
        ]);
        $user = User::where('email', $dataRequest['email'])->first();

        // إرسال OTP عبر البريد الإلكتروني
        // Mail::to($dataRequest['email'])->send(new OtpMail($otp, $user));

        return $this->respondWithSuccess(' OTP has been sent successfully',
            [
                'test-otp-only' => $otpRecord['otp'],
            ]);
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

    public function updatePassword($request,$user)
    {
        $requestPasswordValidated = $request->validated();
        if ($user instanceof User) {
            $user->update(['password' => Hash::make($requestPasswordValidated['password'])]);

            return $this->respondWithSuccess('Password Updated Successfully');
        }
        throw new \Exception('User Not Found Currently!');
    }

}
