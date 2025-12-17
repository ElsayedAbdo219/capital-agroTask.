<?php

namespace Modules\User\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Modules\User\App\Interfaces\UserInterface;
use Modules\User\Http\Requests\V1\UserRequest;
use Modules\User\Models\User;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected $UserRepository;

    public function __construct(UserInterface $UserRepository)
    {
        $this->UserRepository = $UserRepository;
    }

    public function index(Request $request)
    {
        return $this->UserRepository->index();
    }

    public function store(UserRequest $request)
    {
        $this->UserRepository->store($request);

        return $this->respondWithSuccess('User Created Successfully');
    }

    public function show(User $user)
    {
        User::CheckOnThisUser($user);
        $this->UserRepository->show($user);

        return $user->load(['orders']);
    }

    public function update(UserRequest $request, User $user)
    {
        User::CheckOnThisUser($user);
        $this->UserRepository->update($request, $user);

        return $this->respondWithSuccess('User Updated Successfully');
    }

    public function delete(User $user)
    {
        User::CheckOnThisUser($user);
        $this->UserRepository->delete($user);

        return $this->respondWithSuccess('User Deleted Now');
    }
}
