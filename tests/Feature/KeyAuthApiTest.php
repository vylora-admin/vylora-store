<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationUser;
use App\Models\ApplicationVariable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KeyAuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected Application $kvApp;

    protected function setUp(): void
    {
        parent::setUp();
        $owner = User::factory()->create(['role' => 'admin']);
        $this->kvApp = Application::create([
            'owner_id' => $owner->id,
            'name' => 'TestApp',
            'version' => '1.0.0',
            'allow_register' => true,
            'allow_login' => true,
            'allow_extend' => true,
        ]);
    }

    public function test_init_returns_session_id(): void
    {
        $response = $this->post('/api/1.3/', [
            'type' => 'init',
            'name' => $this->kvApp->name,
            'ownerid' => $this->kvApp->owner_uid,
            'secret' => $this->kvApp->secret,
            'ver' => '1.0.0',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['sessionid', 'app']);
    }

    public function test_init_rejects_wrong_secret(): void
    {
        $response = $this->post('/api/1.3/', [
            'type' => 'init',
            'name' => $this->kvApp->name,
            'ownerid' => $this->kvApp->owner_uid,
            'secret' => 'wrong-secret',
            'ver' => '1.0.0',
        ]);

        $response->assertOk()->assertJsonPath('success', false);
    }

    public function test_login_succeeds_with_valid_user(): void
    {
        $user = ApplicationUser::create([
            'application_id' => $this->kvApp->id,
            'username' => 'tester',
            'password_hash' => Hash::make('secret123'),
            'level' => 1,
            'expires_at' => now()->addDays(30),
        ]);

        $init = $this->post('/api/1.3/', [
            'type' => 'init',
            'name' => $this->kvApp->name,
            'ownerid' => $this->kvApp->owner_uid,
            'secret' => $this->kvApp->secret,
            'ver' => '1.0.0',
        ])->json();

        $response = $this->post('/api/1.3/', [
            'type' => 'login',
            'name' => $this->kvApp->name,
            'ownerid' => $this->kvApp->owner_uid,
            'secret' => $this->kvApp->secret,
            'ver' => '1.0.0',
            'sessionid' => $init['sessionid'],
            'username' => 'tester',
            'pass' => 'secret123',
            'hwid' => 'test-hwid',
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertEquals('tester', $response->json('info.username'));
    }

    public function test_var_returns_value_for_authorized_session(): void
    {
        $user = ApplicationUser::create([
            'application_id' => $this->kvApp->id,
            'username' => 'reader',
            'password_hash' => Hash::make('secret123'),
            'level' => 5,
            'expires_at' => now()->addDays(30),
        ]);

        ApplicationVariable::create([
            'application_id' => $this->kvApp->id,
            'scope' => 'global',
            'key' => 'banner',
            'value' => 'hello world',
            'required_level' => 0,
        ]);

        $init = $this->post('/api/1.3/', [
            'type' => 'init', 'name' => $this->kvApp->name, 'ownerid' => $this->kvApp->owner_uid,
            'secret' => $this->kvApp->secret, 'ver' => '1.0.0',
        ])->json();

        $this->post('/api/1.3/', [
            'type' => 'login', 'name' => $this->kvApp->name, 'ownerid' => $this->kvApp->owner_uid,
            'secret' => $this->kvApp->secret, 'ver' => '1.0.0',
            'sessionid' => $init['sessionid'], 'username' => 'reader', 'pass' => 'secret123', 'hwid' => 'h',
        ])->assertJsonPath('success', true);

        $response = $this->post('/api/1.3/', [
            'type' => 'var', 'name' => $this->kvApp->name, 'ownerid' => $this->kvApp->owner_uid,
            'secret' => $this->kvApp->secret, 'ver' => '1.0.0',
            'sessionid' => $init['sessionid'], 'varid' => 'banner',
        ]);

        $response->assertOk()->assertJsonPath('response', 'hello world');
    }

    public function test_unknown_type_returns_error(): void
    {
        $response = $this->post('/api/1.3/', [
            'type' => 'foobar',
            'name' => $this->kvApp->name,
            'ownerid' => $this->kvApp->owner_uid,
            'secret' => $this->kvApp->secret,
            'ver' => '1.0.0',
        ]);
        $response->assertOk()->assertJsonPath('success', false);
    }
}
