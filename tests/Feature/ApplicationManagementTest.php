<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_application(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('applications.store'), [
            'name' => 'CoolApp',
            'version' => '2.0.0',
            'description' => 'Demo',
            'default_subscription_days' => 30,
            'allow_login' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('applications', ['name' => 'CoolApp', 'version' => '2.0.0']);
    }

    public function test_dashboard_renders_for_authenticated_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('dashboard'))->assertOk();
    }

    public function test_application_index_lists_apps(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Application::create([
            'owner_id' => $admin->id,
            'name' => 'ListedApp',
            'version' => '1.0',
        ]);
        $this->actingAs($admin)->get(route('applications.index'))
            ->assertOk()
            ->assertSee('ListedApp');
    }

    public function test_settings_blocks_regular_users(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user)->get(route('settings.index'))->assertStatus(403);
    }
}
