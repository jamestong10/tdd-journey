<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Recipe;
use Hash;
use JWTAuth;

class RecipeTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    //Create user and authenticate the user
    protected function authenticate()
    {
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make('secret1234'),
        ]);
        $token = JWTAuth::fromUser($user);
        return compact('token', 'user');
    }

    //Test the display all routes
    public function testAll()
    {
        //Authenticate and attach recipe to user
        $authData = $this->authenticate();
        $token = $authData['token'];
        $recipe = new Recipe([
            'title' => $this->faker->name,
            'procedure' => $this->faker->realText($this->faker->numberBetween(100, 200))
        ]);
        $user = $authData['user'];
        $user->recipes()->save($recipe);
        //call route and assert response
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', route('recipe.all'));
        $response->assertStatus(200);
        
        $this->assertEquals($recipe->title, $response->json()[0]['title']);
        $this->assertEquals($recipe->procedure, $response->json()[0]['procedure']);
    }

    /**
     * Test the create route
     */
    public function testCreate()
    {
        $authData = $this->authenticate();
        //Get token
        $token = $authData['token'];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('recipe.create'), [
            'title' => $this->faker->name,
            'procedure' => $this->faker->realText($this->faker->numberBetween(100, 200))
        ]);
        $response->assertStatus(200);

        //Get count and assert
        $user = $authData['user'];
        $count = $user->recipes()->count();
        $this->assertEquals(1, $count);
    }

    /**
     * Test the update route
     */
    public function testUpdate()
    {
        $authData = $this->authenticate();
        //Get token
        $token = $authData['token'];
        $recipe = Recipe::create([
            'title' => $this->faker->name,
            'procedure' => $this->faker->realText($this->faker->numberBetween(100, 200))
        ]);
        $user = $authData['user'];
        $user->recipes()->save($recipe);

        $params = [
            'title' => $this->faker->name,
            'procedure' => $this->faker->realText($this->faker->numberBetween(100, 200))
        ];
        
        //call route and assert response
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('recipe.update', ['recipe' => $recipe->id]), $params);
        $response->assertStatus(200);

        //Assert title is the new title
        $this->assertEquals($params['title'], $response->json()['title']);
        $this->assertEquals($params['procedure'], $response->json()['procedure']);
    }

    //Test the single show route
    public function testShow()
    {
        $authData = $this->authenticate();
        //Get token
        $token = $authData['token'];
        $recipe = Recipe::create([
            'title' => $this->faker->name,
            'procedure' => $this->faker->realText($this->faker->numberBetween(100, 200))
        ]);
        $user = $authData['user'];
        $user->recipes()->save($recipe);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', route('recipe.show', ['recipe' => $recipe->id]));
        $response->assertStatus(200);
        //Assert title is correct
        $this->assertEquals($recipe->title, $response->json()['title']);
    }

    //Test the delete route
    public function testDelete()
    {
        $authData = $this->authenticate();
        //Get token
        $token = $authData['token'];
        $recipe = Recipe::create([
            'title' => $this->faker->name,
            'procedure' => $this->faker->realText($this->faker->numberBetween(100, 200))
        ]);
        $user = $authData['user'];
        $user->recipes()->save($recipe);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('recipe.delete', ['recipe' => $recipe->id]));
        $response->assertStatus(200);
        //Assert there are no recipes
        $this->assertEquals(0, $user->recipes()->count());
    }
}
