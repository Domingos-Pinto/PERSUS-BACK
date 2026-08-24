<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Settings', description: 'Configurações gerais do site')]
class SettingController extends Controller
{
    public function __construct(private SettingService $settingService) {}

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
  
        $httpUrlRule = ['nullable', 'url', 'regex:/^https?:\/\//i'];

        $validated = $request->validate([
            'phone'             => ['nullable', 'string', 'max:50'],
            'email'             => ['nullable', 'email', 'max:255'],
            'address'           => ['nullable', 'string', 'max:255'],
            'whatsapp_link'     => $httpUrlRule,
            'instagram_link'    => $httpUrlRule,
            'facebook_link'     => $httpUrlRule,
            'maintenance_mode'  => ['nullable', 'boolean'],
            'welcome_hero_image'      => ['nullable', 'image', 'max:5120'],
            'welcome_secondary_image' => ['nullable', 'image', 'max:5120'],
            'about_image'             => ['nullable', 'image', 'max:5120'],
            // Novos: galeria da secção "Sobre" (2 imagens secundárias)
            'about_image_2'           => ['nullable', 'image', 'max:5120'],
            'about_image_3'           => ['nullable', 'image', 'max:5120'],
            // Novos: fotos decorativas do rodapé (esquerda/direita)
            'footer_image_left'       => ['nullable', 'image', 'max:5120'],
            'footer_image_right'      => ['nullable', 'image', 'max:5120'],
        ], [
            'whatsapp_link.regex'  => 'O link do WhatsApp tem de começar por http:// ou https://',
            'instagram_link.regex' => 'O link do Instagram tem de começar por http:// ou https://',
            'facebook_link.regex'  => 'O link do Facebook tem de começar por http:// ou https://',
        ]);

    
        if (Auth::user()->role !== Role::ADMIN) {
            unset(
                $validated['phone'],
                $validated['email'],
                $validated['address'],
                $validated['whatsapp_link'],
                $validated['instagram_link'],
                $validated['facebook_link'],
                $validated['maintenance_mode']
            );
        }

        $images = [
            'welcome_hero_image' => $request->file('welcome_hero_image'),
            'welcome_secondary_image' => $request->file('welcome_secondary_image'),
            'about_image' => $request->file('about_image'),
            'about_image_2' => $request->file('about_image_2'),
            'about_image_3' => $request->file('about_image_3'),
            'footer_image_left' => $request->file('footer_image_left'),
            'footer_image_right' => $request->file('footer_image_right'),
        ];
        unset(
            $validated['welcome_hero_image'],
            $validated['welcome_secondary_image'],
            $validated['about_image'],
            $validated['about_image_2'],
            $validated['about_image_3'],
            $validated['footer_image_left'],
            $validated['footer_image_right']
        );

        $setting = $this->settingService->update($validated, $images);

        return response()->json($setting);
    }
}