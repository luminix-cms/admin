<?php

namespace Workbench\App\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Luminix\Admin\Support\Unpkg;
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

    public function test_access_protected_route_without_authentication()
    {
        $this->get('/dashboard')
            ->assertStatus(302)
            ->assertRedirect('/login');
    }

    public function test_access_protected_route_with_authentication()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user, 'web')->get('/dashboard');

        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString(Unpkg::url('bundle/mui-cms.bundle.iife.js'), $html);
        $this->assertStringContainsString(Unpkg::url('bundle/style.css'), $html);
    }

    public function test_children_routes_are_protected()
    {
        $this->json('GET', '/dashboard/settings/profile')
            ->assertStatus(401);
    }

    public function test_children_routes_are_accessible_when_authenticated()
    {
        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user, 'web')
            ->get('/dashboard/settings/profile')
            ->assertStatus(200);
    }

    public function test_default_admin_url_is_not_registered()
    {
        $this->json('GET', '/admin')
            ->assertStatus(404);
    }
}
