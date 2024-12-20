<?php

use Knuckles\Scribe\Extracting\Strategies;

return [
    'title' => 'EzexStore API Documentation',

    'description' => 'API Documentation for EzexStore Backend System',

    'base_url' => env('APP_URL', 'http://ezexstore-back.test'),


    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/*'],
                'domains' => ['*'],
                'versions' => ['v1'],
            ],
            'include' => [],
            'exclude' => [],
        ],
    ],

    'type' => 'laravel',

    'theme' => 'elements',

    'logo' => '/img/logo.png',

    'static' => [
        'output_path' => 'public/docs',
    ],

    'laravel' => [
        'add_routes' => true,
        'docs_url' => '/docs',
        'assets_directory' => null,
        'middleware' => [],
    ],

    'try_it_out' => [
        'enabled' => true,
        'base_url' => env('APP_URL', 'http://ezexstore-back.test'),
        'use_csrf' => false,
        'csrf_url' => '/sanctum/csrf-cookie',
    ],

    'auth' => [
        'enabled' => true,
        'default' => true,
        'in' => 'header',
        'name' => 'X-API-KEY',
        'use_value' => env('SCRIBE_AUTH_KEY'),
        'placeholder' => '{YOUR_API_KEY}',
        'extra_info' => 'Todas las peticiones deben incluir una API key válida en el header X-API-KEY.',
    ],

    'intro_text' => <<<INTRO
Esta documentación proporciona toda la información necesaria para trabajar con la API de EzexStore.

La API utiliza autenticación mediante API Keys y maneja datos específicos por tenant.

## Autenticación
- Todas las peticiones deben incluir el header `X-API-KEY`
- Las API Keys tienen scopes específicos que determinan los permisos

## Scopes Disponibles
- products:read - Lectura de productos
- products:write - Creación/Modificación de productos
- inventory:read - Consulta de inventario
- inventory:write - Modificación de inventario
INTRO,

    'example_languages' => [
        'bash',
        'javascript',
        'php'
    ],

    'postman' => [
        'enabled' => true,
        'overrides' => [],
    ],

    'openapi' => [
        'enabled' => true,
        'overrides' => [],
    ],

    'groups' => [
        'default' => 'Endpoints',
        'order' => [
            'Authentication',
            'Tenants',
            'Products',
            'Product Photos',
            'Inventory',
            'Warehouses'
        ],
    ],

    'last_updated' => 'Last updated: {date:F j, Y}',

    'examples' => [
        'faker_seed' => null,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    'strategies' => [
        'metadata' => [
            Strategies\Metadata\GetFromDocBlocks::class,
            Strategies\Metadata\GetFromMetadataAttributes::class,
        ],
        'urlParameters' => [
            Strategies\UrlParameters\GetFromLaravelAPI::class,
            Strategies\UrlParameters\GetFromUrlParamAttribute::class,
            Strategies\UrlParameters\GetFromUrlParamTag::class,
        ],
        'queryParameters' => [
            Strategies\QueryParameters\GetFromFormRequest::class,
            Strategies\QueryParameters\GetFromInlineValidator::class,
            Strategies\QueryParameters\GetFromQueryParamAttribute::class,
            Strategies\QueryParameters\GetFromQueryParamTag::class,
        ],
        'headers' => [
            Strategies\Headers\GetFromHeaderAttribute::class,
            Strategies\Headers\GetFromHeaderTag::class,
            [
                'override',
                [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-API-KEY' => '{YOUR_API_KEY}'
                ]
            ]
        ],
        'bodyParameters' => [
            Strategies\BodyParameters\GetFromFormRequest::class,
            Strategies\BodyParameters\GetFromInlineValidator::class,
            Strategies\BodyParameters\GetFromBodyParamAttribute::class,
            Strategies\BodyParameters\GetFromBodyParamTag::class,
        ],
        'responses' => [
            Strategies\Responses\UseResponseAttributes::class,
            Strategies\Responses\UseTransformerTags::class,
            Strategies\Responses\UseApiResourceTags::class,
            Strategies\Responses\UseResponseTag::class,
            Strategies\Responses\UseResponseFileTag::class,
            [
                Strategies\Responses\ResponseCalls::class,
                [
                    'only' => ['GET *'],
                    'config' => [
                        'app.debug' => false,
                    ],
                ]
            ]
        ],
        'responseFields' => [
            Strategies\ResponseFields\GetFromResponseFieldAttribute::class,
            Strategies\ResponseFields\GetFromResponseFieldTag::class,
        ],
    ],

    'database_connections_to_transact' => [config('database.default')],

    'fractal' => [
        'serializer' => null,
    ],

    'routeMatcher' => \Knuckles\Scribe\Matching\RouteMatcher::class,
    'external' => ['html_attributes' => []],
];
