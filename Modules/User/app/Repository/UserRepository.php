<?php

namespace Modules\User\App\Repository;

use Modules\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Modules\User\App\Interfaces\UserInterface;

class UserRepository implements UserInterface
{
    public function index()
    {
        return User::orderByDesc('created_at')->cursorPaginate($request->paginateSize ?? 10);
    }

    public function store($request)
    {
        User::create(array_merge(
            $request->validated(),
            [
                'email_verified_at' => now(),
            ]
        ));

    }

    public function show($user)
    {
        return $user->load(['orders']);
    }

    public function update($request, $user)
    {
        $user->update($request->validated());
    }

    public function delete($user)
    {
        $user->delete();
    }
}
