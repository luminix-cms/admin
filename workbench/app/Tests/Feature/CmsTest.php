<?php

namespace Workbench\App\Tests\Feature;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Mockery;
use Workbench\App\Models\User;
use Workbench\App\Tests\TestCase;

class CmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_routes_are_protected()
    {
        $this->json('GET', '/admin')
            ->assertStatus(401);
    }

    /**
     * // Acesso rota protegida sem autenticação.
     */
    public function test_access_protected_route_without_authentication()
    {
        $response = $this->get('/admin');

        $response->assertStatus(302);

        $response->assertRedirect('/login');
    }

    // public function test_login_via_endpoint()
    // {
    //     $user = User::create([
    //         'name' => 'John Doe',
    //         'email' => 'john@example.com',
    //         'password' => Hash::make('password'),
    //     ]);

    //     $this->assertDatabaseHas('users', [
    //         'name' => 'John Doe',
    //         'email' => 'john@example.com',
    //     ]);

    // $response = $this->post('/login', [
    //     'email' => 'john@example.com',
    //     'password' => 'password',
    // ]);
    // dd(Auth::user()->name . '    ->  ééééé');

    // dd($response->headers->get('location'), $response->getStatusCode());
    //     $response->assertStatus(200);

    //     // $response->assertRedirect('/admin');
    // }

    public function test_access_protected_route_with_authentication()
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);
        // dd(User::first());

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $response = $this->actingAs($user, 'web')->get('/admin');

        // $this->json('GET', '/admin')
        // ->assertStatus(403);
        // dd($response->headers->get('location'), $response->getStatusCode());
        // $response->assertRedirect('/admin');

        $response->assertStatus(200); // Acesso autorizado
        // $response->assertSee('<script');
    }
}
