<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        // Login dan dapatkan token
        $this->user = User::where('email', 'admin@example.com')->first();
        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password'
        ]);

        $this->token = $response->json('data.token');
    }

    /** @test */
    public function it_can_list_all_brands()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/brands');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'current_page',
                'per_page',
                'total',
                'last_page'
            ]);
    }

    /** @test */
    public function it_can_search_brands()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/brands?search=nike');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'current_page',
                'per_page',
                'total',
                'last_page'
            ]);
    }

    /** @test */
    public function it_can_create_a_brand()
    {
        $data = [
            'name' => 'Test Brand',
            'description' => 'Test Brand Description',
            'website' => 'https://testbrand.com',
            'logo' => 'test-logo.png'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/brands', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);

        $this->assertDatabaseHas('brands', [
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'description' => 'Test Brand Description',
            'website' => 'https://testbrand.com',
            'logo' => 'test-logo.png'
        ]);
    }

    /** @test */
    public function it_can_show_a_brand()
    {
        $brand = Brand::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/brands/' . $brand->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);
    }

    /** @test */
    public function it_can_update_a_brand()
    {
        $brand = Brand::first();
        $data = [
            'name' => 'Updated Brand',
            'description' => 'Updated Brand Description',
            'website' => 'https://updatedbrand.com',
            'logo' => 'updated-logo.png'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->putJson('/api/brands/' . $brand->id, $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);

        $this->assertDatabaseHas('brands', [
            'name' => 'Updated Brand',
            'slug' => 'updated-brand',
            'description' => 'Updated Brand Description',
            'website' => 'https://updatedbrand.com',
            'logo' => 'updated-logo.png'
        ]);
    }

    /** @test */
    public function it_can_delete_a_brand()
    {
        $brand = Brand::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->deleteJson('/api/brands/' . $brand->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ]);

        $this->assertSoftDeleted('brands', ['id' => $brand->id]);
    }

    public function test_it_cannot_access_without_authentication()
    {
        $response = $this->getJson('/api/brands');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }
}