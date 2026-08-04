<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone' => $data['phone'],
            'role' => Role::EDITOR,
        ]);
        Auth::login($user);

        return ['user' => $user];
    }

    public function update(array $data)
{
    $user = Auth::user();

    if (!$user) {
        return ['error' => 'Não autenticado'];
    }

    $user->update($data);

    return ['user' => $user];
}

  public function block(User $target)
{
    $target->status = Status::BLOCKED;
    $target->save();

    return ['message' => "{$target->name} bloqueado"];
}

public function unblock(User $target)
{
    $target->status = Status::UNBLOCKED;
    $target->save();

    return ['message' => "{$target->name} desbloqueado"];
}

    public function login(array $credential)
    {
        $field = filter_var($credential['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (!Auth::attempt([$field => $credential['login'], 'password' => $credential['password']])) {
            return null;
        }

        if (Auth::user()->status === Status::BLOCKED) {
            Auth::guard('web')->logout();
            return ['blocked' => true];
        }

        return ['user' => Auth::user()];
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return ['message' => 'sessão terminada'];
    }

    public function me(): array
    {
        $user = Auth::user();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'status' => $user->status,
        ];
    }
}