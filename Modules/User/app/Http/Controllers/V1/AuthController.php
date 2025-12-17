<?php

namespace Modules\User\Http\Controllers\V1;

use Illuminate\Http\Request;
use Modules\User\Models\User;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Modules\User\App\Services\AuthService;
use Modules\User\Http\Requests\V1\LoginClientRequest;
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
    }

    public function refreshToken(Request $request)
    {
        $this->AuthService->refreshToken($request);
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

    public function forgetPassword(Request $request)
    {
      $this->AuthService->forgetPassword($request);
    }
    
    public function resetPassword(Request $request)
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
