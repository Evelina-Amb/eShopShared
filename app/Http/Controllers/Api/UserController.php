<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\BaseCollection;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAll();
        return $this->sendResponse(new BaseCollection($users, UserResource::class), 'Users retrieved.');
    }

    public function show($id)
    {
        $user = $this->userService->getById($id);
        if (!$user) return $this->sendError('User not found.', 404);

        return $this->sendResponse(new UserResource($user), 'User found.');
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->create($request->validated());
        return $this->sendResponse(new UserResource($user), 'User created.', 201);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->userService->update($id, $request->validated());
        if (!$user) return $this->sendError('User not found.', 404);

        return $this->sendResponse(new UserResource($user), 'User updated.');
    }

    public function destroy($id)
    {
        $deleted = $this->userService->delete($id);
        if (!$deleted) return $this->sendError('User not found.', 404);

        return $this->sendResponse(null, 'User deleted.');
    }

    public function ban(Request $request, $id)
{
    $user = User::find($id);
    if (!$user) {
        return $this->sendError('User not found.', 404);
    }

    $user->update([
        'is_banned' => true,
        'ban_reason' => $request->reason ?? 'No reason provided',
        'banned_at' => now()
    ]);

    return $this->sendResponse(new UserResource($user), 'User banned.');
}

public function unban($id)
{
    $user = User::find($id);
    if (!$user) {
        return $this->sendError('User not found.', 404);
    }

    $user->update([
        'is_banned' => false,
        'ban_reason' => null,
        'banned_at' => null
    ]);

    return $this->sendResponse(new UserResource($user), 'User unbanned.');
}
}
