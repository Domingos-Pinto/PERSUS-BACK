<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

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

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
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

    public function forgotPassword(string $email): bool
    {
        $status = Password::sendResetLink(['email' => $email]);

        return $status === Password::RESET_LINK_SENT;
    }

    public function resetPassword(array $data): bool
    {
        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET;
    }

    // Lista de funcionários (role editor) para o painel de admin.
    public function listEmployees()
    {
        return User::where('role', Role::EDITOR)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone', 'status']);
    }

  
    public function createEmployee(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone' => $data['phone'],
            'role' => Role::EDITOR,
        ]);

        return ['user' => $user];
    }
}
