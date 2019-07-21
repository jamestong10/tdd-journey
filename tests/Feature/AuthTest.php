<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class AuthTest extends TestCase
{
    use WithFaker;
    /**
     * @test
     * Test registration
     */
    public function testRegister()
    {
        //User's data
        $data = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ];
        //Send post request
        $response = $this->json('POST', route('api.register'), $data);
        //Assert it was successful
        $response->assertStatus(200);
        //Assert we received a token
        $this->assertArrayHasKey('token', $response->json());
        //Delete data
        User::where('email', $data['email'])->delete();
    }
    /**
     * @test
     * Test login
     */
    public function testLogin()
    {
        //Create user
        $user = User::create([
            'name' => $this->faker->name,
            'email'=> $this->faker->email,
            'password' => bcrypt('secret1234')
        ]);
        //attempt login
        $response = $this->json('POST', route('api.authenticate'), [
            'email' => $user->email,
            'password' => 'secret1234',
        ]);
        //Assert it was successful and a token was received
        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response->json());
        //Delete the user
        $user->delete();
    }
}
