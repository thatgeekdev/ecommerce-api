<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Caminhos habilitados para CORS
    |--------------------------------------------------------------------------
    |
    | Defina os endpoints da sua aplicação que devem aceitar requisições CORS.
    | Exemplo: ['api/*', 'sanctum/csrf-cookie']
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Métodos permitidos
    |--------------------------------------------------------------------------
    |
    | Quais métodos HTTP podem ser usados nas requisições CORS.
    | '*' significa todos.
    |
    */

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Origens permitidas
    |--------------------------------------------------------------------------
    |
    | Defina as origens (domínios) que podem consumir a API.
    | É possível definir via .env -> CORS_ALLOWED_ORIGINS
    |
    | Exemplo no .env:
    | CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:5173,https://meuecommerce.com
    |
    */

    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://localhost:5173')),

    /*
    |--------------------------------------------------------------------------
    | Cabeçalhos permitidos
    |--------------------------------------------------------------------------
    |
    | Defina os headers aceitos nas requisições CORS.
    | '*' significa todos.
    |
    */

    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Cabeçalhos expostos
    |--------------------------------------------------------------------------
    |
    | Quais cabeçalhos serão expostos ao navegador.
    |
    */

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Tempo máximo de cache da preflight request
    |--------------------------------------------------------------------------
    |
    | Tempo (em segundos) que o navegador deve armazenar a resposta CORS
    | antes de executar uma nova preflight request.
    |
    */

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Suporte a credenciais
    |--------------------------------------------------------------------------
    |
    | Se "true", cookies e credenciais podem ser enviados junto da requisição.
    | Geralmente deve ser "true" para apps SPA autenticadas via Sanctum.
    |
    */

    'supports_credentials' => true,

];
