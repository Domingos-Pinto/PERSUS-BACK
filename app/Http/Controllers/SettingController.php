<?php

namespace App\Http\Controllers;

use App\Services\SettingService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Settings', description: 'Configurações gerais do site')]
class SettingController extends Controller
{
    public function __construct(private SettingService $settingService)
    {
    }

    #[OA\Get(
        path: '/api/settings',
        summary: 'Mostra as configurações atuais do site',
        tags: ['Settings'],
        responses: [
            new OA\Response(response: 200, description: 'Configurações do site')
        ]
    )]
    public function show()
    {
        return response()->json($this->settingService->get());
    }

    #[OA\Put(
        path: '/api/settings',
        summary: 'Atualiza as configurações do site',
        security: [['sanctumCsrf' => []]],
        tags: ['Settings'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'phone', type: 'string', nullable: true),
                        new OA\Property(property: 'email', type: 'string', nullable: true),
                        new OA\Property(property: 'address', type: 'string', nullable: true),
                        new OA\Property(property: 'whatsapp_link', type: 'string', nullable: true),
                        new OA\Property(property: 'instagram_link', type: 'string', nullable: true),
                        new OA\Property(property: 'facebook_link', type: 'string', nullable: true),
                        new OA\Property(property: 'maintenance_mode', type: 'boolean', nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Configurações atualizadas com sucesso'),
            new OA\Response(response: 422, description: 'Erro de validação')
        ]
    )]
    public function update(Request $request)
    {
        $validated = $request->validate([
            'phone'             => ['nullable', 'string', 'max:50'],
            'email'             => ['nullable', 'email', 'max:255'],
            'address'           => ['nullable', 'string', 'max:255'],
            'whatsapp_link'     => ['nullable', 'url'],
            'instagram_link'    => ['nullable', 'url'],
            'facebook_link'     => ['nullable', 'url'],
            'maintenance_mode'  => ['nullable', 'boolean'],
        ]);

        $setting = $this->settingService->update($validated);

        return response()->json($setting);
    }
}