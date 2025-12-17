<?php

namespace Modules\User\Http\Controllers\V1;

use Illuminate\Http\Request;
use Modules\User\Models\User;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Http\Requests\V1\UserRequest;

class UserController extends Controller
{
     use ApiResponseTrait;

    public function index(Request $request)
    {
        return User::orderByDesc('created_at')->cursorPaginate($request->paginateSize ?? 10);
    }

    public function store(UserRequest $request)
    {
       User::create($request->validated());
       return $this->respondWithSuccess('User Created Successfully');
    }

  
    public function show(User $user)
    {
        User::CheckOnThisUser($user);
        return $user->load(['orders']);
    }

  
    public function update(Request $request, User $user)
    {
       User::CheckOnThisUser($user);
       $user->update($request->validated());
       return $this->respondWithSuccess('User Updated Successfully');
    }

  
    public function destroy(User $user) 
    {
       User::CheckOnThisUser($user);
       $user->delete();
       return $this->respondWithSuccess('User Deleted Now');
    }
    
}
