<?php

namespace Modules\User\App\Interfaces;

use Modules\User\Models\User;

abstract class UserRepository implements UserInterface
{
    public function index()
    {
        return User::orderByDesc('created_at')->cursorPaginate($request->paginateSize ?? 10);
    }

    public function store($request)
    {
        User::create($request->validated());
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
