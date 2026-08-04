<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

#[OA\Info(title: "Prohigienizar API", version: "1.0.0")]
class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    #[OA\Post(
        path: "/api/register",
        summary: "Regista um novo utilizador",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "phone"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Domingos"),
                    new OA\Property(property: "email", type: "string", example: "teste@teste.com"),
                    new OA\Property(property: "password", type: "string", example: "123456"),
                    new OA\Property(property: "phone", type: "string", example: "959697512"),
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(response: 201, description: "Utilizador criado com sucesso"),
            new OA\Response(response: 422, description: "Erro de validação"),
        ]
    )]
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required|string|unique:users',
        ]);

        return response()->json($this->authService->register($data), 201);
    }

    #[OA\Post(
        path: "/api/login",
        summary: "Autentica um utilizador (email ou telefone)",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["login", "password"],
                properties: [
                    new OA\Property(property: "login", type: "string", example: "teste@teste.com ou 959697512"),
                    new OA\Property(property: "password", type: "string", example: "123456"),
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(response: 200, description: "Login bem-sucedido"),
            new OA\Response(response: 401, description: "Credenciais erradas"),
            new OA\Response(response: 403, description: "Conta bloqueada"),
        ]
    )]
    public function login(Request $request)
    {
        $credential = $request->validate([
            'login' => 'required|string',
            'password' => 'required|min:6',
        ]);

        $result = $this->authService->login($credential);

        if (!$result) {
            return response()->json(['message' => 'Credenciais erradas'], 401);
        }

        if (isset($result['blocked'])) {
            return response()->json(['message' => 'Conta bloqueada'], 403);
        }

        return response()->json($result);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Termina a sessão",
        tags: ["Auth"],
        security: [["sanctum" => []]],
        responses: [new OA\Response(response: 200, description: "Sessão terminada")]
    )]
    public function logout()
    {
        return response()->json($this->authService->logout());
    }

    #[OA\Get(
        path: "/api/me",
        summary: "Dados do utilizador autenticado",
        tags: ["Auth"],
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(response: 200, description: "Dados do utilizador"),
            new OA\Response(response: 401, description: "Não autenticado"),
        ]
    )]
    public function me()
    {
        return response()->json($this->authService->me());
    }

    #[OA\Put(
        path: "/api/me",
        summary: "Atualiza dados do utilizador autenticado",
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Domingos Pinto"),
                    new OA\Property(property: "email", type: "string", example: "novo@teste.com"),
                    new OA\Property(property: "phone", type: "string", example: "959697513"),
                ]
            )
        ),
        tags: ["Auth"],
        security: [["sanctum" => []]],
        responses: [new OA\Response(response: 200, description: "Dados atualizados")]
    )]
    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . Auth::id(),
            'phone' => 'sometimes|string|unique:users,phone,' . Auth::id(),
        ]);

        return response()->json($this->authService->update($data));
    }


#[OA\Post(
    path: "/api/users/{user}/block",
    summary: "Bloqueia um utilizador (apenas admin)",
    tags: ["Auth"],
    security: [["sanctum" => []]],
    parameters: [
        new OA\Parameter(
            name: "user",
            description: "ID do utilizador a bloquear",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: "Utilizador bloqueado"),
        new OA\Response(response: 403, description: "Sem permissão"),
    ]
)]

public function block(User $user)
{
    if (Auth::user()->role !== Role::ADMIN) {
        return response()->json(['message' => 'Sem permissão'], 403);
    }

    return response()->json($this->authService->block($user));
}

  #[OA\Post(
    path: "/api/users/{user}/unblock",
    summary: "Desbloqueia um utilizador (apenas admin)",
    tags: ["Auth"],
    security: [["sanctum" => []]],
    parameters: [
        new OA\Parameter(
            name: "user",
            description: "ID do utilizador a desbloquear",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: "Utilizador desbloqueado"),
        new OA\Response(response: 403, description: "Sem permissão"),
    ]
)]
public function unblock(User $user)
{
    if (Auth::user()->role !== Role::ADMIN) {
        return response()->json(['message' => 'Sem permissão'], 403);
    }

    return response()->json($this->authService->unblock($user));
}
}