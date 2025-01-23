<?php

namespace Workbench\App\Tests\Feature;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Luminix\Admin\Support\Unpkg;
use Mockery;
use Workbench\App\Models\User;
use Workbench\App\Tests\TestCase;

class AlternativeConfigurationCmsTest extends TestCase
{
    use RefreshDatabase;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('luminix.admin.url', 'dashboard');
    }

    public function test_routes_are_protected()
    {
        $this->json('GET', '/dashboard')
            ->assertStatus(401);
    }

    /**
     * // Acesso rota protegida sem autenticação.
     */
    public function test_access_protected_route_without_authentication()
    {
        $response = $this->get('/dashboard');

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

    //     // $response->assertRedirect('/dashboard');
    // }

    public function test_access_protected_route_with_authentication()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        
        $response = $this->actingAs($user, 'web')->get('/dashboard');

        // $this->json('GET', '/dashboard')
        // ->assertStatus(403);
        // dd($response->headers->get('location'), $response->getStatusCode());
        // $response->assertRedirect('/dashboard');

        $response->assertStatus(200); // Acesso autorizado
        $response->assertSeeHtml('script', [
            'type' => 'module',
            'crossorigin' => 'crossorigin',
            'src' => Unpkg::url('bundle/mui-cms.bundle.iife.js'),
        ]);
    }

    public function test_children_routes_are_protected()
    {
        $this->json('GET', '/dashboard/children/foo/bar')
            ->assertStatus(401);

        // add teste logado pra rotas aninhadas
    }



}
