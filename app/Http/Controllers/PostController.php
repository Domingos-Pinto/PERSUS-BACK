<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Posts', description: 'Gestão do blog')]
class PostController extends Controller
{
    public function __construct(private PostService $postService)
    {
    }

    #[OA\Get(
        path: '/api/posts',
        summary: 'Lista todas as publicações',
        tags: ['Posts'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de publicações')
        ]
    )]
    public function index()
    {
        return response()->json($this->postService->list());
    }

    #[OA\Get(
        path: '/api/posts/{id}',
        summary: 'Mostra uma publicação específica',
        tags: ['Posts'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Publicação encontrada'),
            new OA\Response(response: 404, description: 'Publicação não encontrada')
        ]
    )]
    public function show(int $id)
    {
        return response()->json($this->postService->find($id));
    }

    #[OA\Post(
        path: '/api/posts',
        summary: 'Cria uma nova publicação',
        security: [['sanctumCsrf' => []]],
        tags: ['Posts'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'title', type: 'string'),
                        new OA\Property(property: 'content', type: 'string'),
                        new OA\Property(property: 'published_at', type: 'string', format: 'date-time', nullable: true),
                        new OA\Property(property: 'cover_image', type: 'string', format: 'binary', nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Publicação criada com sucesso'),
            new OA\Response(response: 422, description: 'Erro de validação')
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'content'       => ['required', 'string'],
            'published_at'  => ['nullable', 'date'],
            'cover_image'   => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $post = $this->postService->create($validated, $request->file('cover_image'));

        return response()->json($post, 201);
    }

    #[OA\Put(
        path: '/api/posts/{id}',
        summary: 'Atualiza uma publicação existente',
        security: [['sanctumCsrf' => []]],
        tags: ['Posts'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'title', type: 'string'),
                        new OA\Property(property: 'content', type: 'string'),
                        new OA\Property(property: 'published_at', type: 'string', format: 'date-time', nullable: true),
                        new OA\Property(property: 'cover_image', type: 'string', format: 'binary', nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Publicação atualizada com sucesso'),
            new OA\Response(response: 404, description: 'Publicação não encontrada'),
            new OA\Response(response: 422, description: 'Erro de validação')
        ]
    )]
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title'         => ['sometimes', 'required', 'string', 'max:255'],
            'content'       => ['sometimes', 'required', 'string'],
            'published_at'  => ['nullable', 'date'],
            'cover_image'   => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $post = $this->postService->update($id, $validated, $request->file('cover_image'));

        return response()->json($post);
    }

    #[OA\Delete(
        path: '/api/posts/{id}',
        summary: 'Apaga uma publicação',
        security: [['sanctumCsrf' => []]],
        tags: ['Posts'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Publicação apagada com sucesso'),
            new OA\Response(response: 404, description: 'Publicação não encontrada')
        ]
    )]
    public function destroy(int $id)
    {
        $this->postService->delete($id);

        return response()->json(null, 204);
    }
}