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

    /**
     * Create user and authenticate the user
     */
    protected function authenticate()
    {
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);
        return compact('token', 'user');
    }

    /**
     * Test the all route
     */
    public function testAll()
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $recipe = factory(Recipe::class)->make();
        $user = $authData['user'];
        $user->recipes()->save($recipe);
        
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
        $token = $authData['token'];
        $recipe = factory(Recipe::class)->make();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('recipe.create'), $recipe->toArray());

        $response->assertStatus(200);

        // get count and assert
        $user = $authData['user'];
        $user->refresh();
        $count = $user->recipes()->count();
        $this->assertEquals(1, $count);
    }

    /**
     * Test the update route
     */
    public function testUpdate()
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        list($recipe, $newRecipe) = factory(Recipe::class, 2)->make();
        
        $user = $authData['user'];
        $user->recipes()->save($recipe);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('recipe.update', ['recipe' => $recipe->id]), $newRecipe->toArray());

        $response->assertStatus(200);

        // assert title is the new title
        $this->assertEquals($newRecipe->title, $response->json()['title']);
        $this->assertEquals($newRecipe->procedure, $response->json()['procedure']);
    }

    /**
     * Test the show route
     */
    public function testShow()
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $recipe = factory(Recipe::class)->make();
        $user = $authData['user'];
        $user->recipes()->save($recipe);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', route('recipe.show', ['recipe' => $recipe->id]));

        $response->assertStatus(200);

        // assert title is correct
        $this->assertEquals($recipe->title, $response->json()['title']);
    }

    /**
     * Test the delete route
     */
    public function testDelete()
    {
        $authData = $this->authenticate();
        $token = $authData['token'];
        $recipe = factory(Recipe::class)->make();
        $user = $authData['user'];
        $user->recipes()->save($recipe);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', route('recipe.delete', ['recipe' => $recipe->id]));

        $response->assertStatus(200);

        // assert there are no recipes
        $this->assertEquals(0, $user->recipes()->count());
    }
}
