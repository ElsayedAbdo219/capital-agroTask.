<?php

namespace Modules\User\Http\Controllers\V1;

use Illuminate\Http\Request;
use Modules\User\Models\User;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Modules\User\App\Services\AuthService;
use Modules\User\Http\Requests\V1\LoginClientRequest;
use Modules\User\Http\Requests\V1\ResetPasswordRequest;
use Modules\User\Http\Requests\V1\ForgetPasswordRequest;
use Modules\User\Http\Requests\V1\UpdatePasswordRequest;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected $AuthService;

    public function __construct(AuthService $AuthService)
    {
        $this->AuthService = $AuthService;
    }

    public function login(LoginClientRequest $request)
    {
        $this->AuthService->login($request);
        $user = User::where('email', $request->email)->first();
        $user->tokens()->delete();
        $accessToken = $user->createToken('access-token', ['*'], now()->addMinutes(60))->plainTextToken;
        $refreshToken = $user->createToken('refresh-token', ['refresh'], now()->addDays(7))->plainTextToken;

        return $this->respondWithSuccess('User Logged In Successfully', [
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 60, // 1 ساعة
        ]);
    }

    public function refreshToken(Request $request)
    {
        $this->AuthService->refreshToken($request);
        $user = auth('api')->user();
        $newAccessToken = $user->createToken('access-token', ['*'], now()->addMinutes(60))->plainTextToken;
        $newRefreshToken = $user->createToken('refresh-token', ['refresh'], now()->addDays(7))->plainTextToken;
        
        return response()->json([
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 60,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $this->AuthService->refreshToken($request);
        return $this->respondWithSuccess('User verified successfully!');
    }

    public function verifyAccount(Request $request)
    {
      $this->AuthService->verifyAccount($request);
    }

    public function resendOtp(Request $request)
    {
      $this->AuthService->resendOtp($request);
    }

    public function forgetPassword(ForgetPasswordRequest $request)
    {
      $this->AuthService->forgetPassword($request);
    }
    
    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->AuthService->resetPassword($request);
    }

    public function me()
    {
      $this->AuthService->me();  
    }

    public function updatePassword(UpdatePasswordRequest $request,User $user)
    {
      $this->AuthService->me($request,$user);  
    }
  
}
