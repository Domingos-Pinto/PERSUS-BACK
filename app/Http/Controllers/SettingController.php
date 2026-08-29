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

        $imageRule = [
            'nullable',
            'file',
            'mimes:jpg,jpeg,png,gif,bmp,svg,webp,heic,heif,tiff,tif,avif,ico',
            'max:8192', 
        ];

        $validated = $request->validate([
            'phone'             => ['nullable', 'string', 'max:50'],
            'email'             => ['nullable', 'email', 'max:255'],
            'address'           => ['nullable', 'string', 'max:255'],
            'whatsapp_link'     => $httpUrlRule,
            'instagram_link'    => $httpUrlRule,
            'facebook_link'     => $httpUrlRule,
            'maintenance_mode'  => ['nullable', 'boolean'],
            'welcome_hero_image'      => $imageRule,
            'welcome_secondary_image' => $imageRule,
            'about_image'             => $imageRule,
            'about_image_2'           => $imageRule,
            'about_image_3'           => $imageRule,
            'footer_image_left'       => $imageRule,
            'footer_image_right'      => $imageRule,
        ], [
            'whatsapp_link.regex'  => 'O link do WhatsApp tem de começar por http:// ou https://',
            'instagram_link.regex' => 'O link do Instagram tem de começar por http:// ou https://',
            'facebook_link.regex'  => 'O link do Facebook tem de começar por http:// ou https://',
            'welcome_hero_image.mimes'      => 'Formato de imagem não suportado.',
            'welcome_secondary_image.mimes' => 'Formato de imagem não suportado.',
            'about_image.mimes'             => 'Formato de imagem não suportado.',
            'about_image_2.mimes'           => 'Formato de imagem não suportado.',
            'about_image_3.mimes'           => 'Formato de imagem não suportado.',
            'footer_image_left.mimes'       => 'Formato de imagem não suportado.',
            'footer_image_right.mimes'      => 'Formato de imagem não suportado.',
            'welcome_hero_image.max'      => 'A imagem é demasiado grande (máx. 8MB).',
            'welcome_secondary_image.max' => 'A imagem é demasiado grande (máx. 8MB).',
            'about_image.max'             => 'A imagem é demasiado grande (máx. 8MB).',
            'about_image_2.max'           => 'A imagem é demasiado grande (máx. 8MB).',
            'about_image_3.max'           => 'A imagem é demasiado grande (máx. 8MB).',
            'footer_image_left.max'       => 'A imagem é demasiado grande (máx. 8MB).',
            'footer_image_right.max'      => 'A imagem é demasiado grande (máx. 8MB).',
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