<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class AuthTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * @test
     * Test registration
     */
    public function testRegister()
    {
        $userData = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ];

        // send post request
        $response = $this->json('POST', route('api.register'), $userData);
        
        // assert it was successful
        $response->assertStatus(200);
        
        // assert we received a token
        $this->assertArrayHasKey('token', $response->json());
    }
    
    /**
     * @test
     * Test login
     */
    public function testLogin()
    {
        $user = factory(User::class)->create();

        // attempt login
        $response = $this->json('POST', route('api.authenticate'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        // assert it was successful and a token was received
        $response->assertStatus(200);

        // assert we received a token
        $this->assertArrayHasKey('token', $response->json());
    }
}
