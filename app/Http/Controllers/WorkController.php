<?php

namespace App\Http\Controllers;

use App\Services\WorkService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Works', description: 'Gestão de trabalhos/portfólio')]
class WorkController extends Controller
{
    public function __construct(private WorkService $workService)
    {
    }

    #[OA\Get(
        path: '/api/works',
        summary: 'Lista todos os trabalhos',
        tags: ['Works'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de trabalhos com imagens')
        ]
    )]
    public function index()
    {
        return response()->json($this->workService->list());
    }

    #[OA\Get(
        path: '/api/works/{id}',
        summary: 'Mostra um trabalho específico',
        tags: ['Works'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Trabalho encontrado'),
            new OA\Response(response: 404, description: 'Trabalho não encontrado')
        ]
    )]
    public function show(int $id)
    {
        return response()->json($this->workService->find($id));
    }

    #[OA\Post(
        path: '/api/works',
        summary: 'Cria um novo trabalho',
        security: [['sanctumCsrf' => []]],
        tags: ['Works'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'title', type: 'string'),
                        new OA\Property(property: 'category', type: 'string', enum: ['tv_panel', 'custom_furniture', 'false_ceiling', 'letters_3d', 'signs_3d']),
                        new OA\Property(property: 'description', type: 'string', nullable: true),
                        new OA\Property(property: 'images', type: 'array', items: new OA\Items(type: 'string', format: 'binary')),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Trabalho criado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Erro de validação')
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string', 'in:tv_panel,custom_furniture,false_ceiling,letters_3d,signs_3d'],
            'description' => ['nullable', 'string'],
            'images'      => ['nullable', 'array'],
            'images.*'    => ['file', 'image', 'max:5120'],
        ]);

        $work = $this->workService->create($validated, $request->file('images', []));

        return response()->json($work, 201);
    }

    #[OA\Put(
        path: '/api/works/{id}',
        summary: 'Atualiza um trabalho existente',
        security: [['sanctumCsrf' => []]],
        tags: ['Works'],
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
                        new OA\Property(property: 'category', type: 'string', enum: ['tv_panel', 'custom_furniture', 'false_ceiling', 'letters_3d', 'signs_3d']),
                        new OA\Property(property: 'description', type: 'string', nullable: true),
                        new OA\Property(property: 'images', type: 'array', items: new OA\Items(type: 'string', format: 'binary')),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Trabalho atualizado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Trabalho não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação')
        ]
    )]
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title'       => ['sometimes', 'required', 'string', 'max:255'],
            'category'    => ['sometimes', 'required', 'string', 'in:tv_panel,custom_furniture,false_ceiling,letters_3d,signs_3d'],
            'description' => ['nullable', 'string'],
            'images'      => ['nullable', 'array'],
            'images.*'    => ['file', 'image', 'max:5120'],
        ]);

        $work = $this->workService->update($id, $validated, $request->file('images', []));

        return response()->json($work);
    }

    #[OA\Delete(
        path: '/api/works/{id}',
        summary: 'Apaga um trabalho',
        security: [['sanctumCsrf' => []]],
        tags: ['Works'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Trabalho apagado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Trabalho não encontrado')
        ]
    )]
    public function destroy(int $id)
    {
        $this->workService->delete($id);

        return response()->json(null, 204);
    }
}