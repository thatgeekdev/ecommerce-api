<?php
namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function it_registra_e_autentica_um_utilizador()
    {
        // Payload de registro
        $payload = [
            'name' => 'Jose',
            'email' => 'jose@example.com',
            'password' => 'secret123',
        ];

        // Registro
        $this->postJson('/api/v1/auth/register', $payload)
            ->assertCreated();

        // Login
        $this->postJson('/api/v1/auth/login', [
            'email' => $payload['email'],
            'password' => $payload['password'],
        ])
        ->assertOk()
        ->assertJsonStructure(['token']);
    }
}
