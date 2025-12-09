<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;


class UserService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAll()
    {
        return $this->userRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->userRepository->getById($id);
    }

    public function create(array $data)
    {
        return $this->userRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $user = $this->userRepository->getById($id);
        if (!$user) return null;

        return $this->userRepository->update($user, $data);
    }

    public function delete(int $id)
    {
        $user = $this->userRepository->getById($id);
        if (!$user) return false;

        return $this->userRepository->delete($user);
    }

   public function banUser(User $user, ?string $reason = null)
{
    $user = \App\Models\User::find($userId);
    if (!$user) {
        throw new \Exception("User not found");
    }

    $user->update([
        'is_banned' => true,
        'ban_reason' => $reason,
        'banned_at' => now()
    ]);

    return $user;
}

public function unbanUser(int $userId)
{
    $user = \App\Models\User::find($userId);
    if (!$user) {
        throw new \Exception("User not found");
    }

    $user->update([
        'is_banned' => false,
        'ban_reason' => null,
        'banned_at' => null
    ]);

    return $user;
}

}
