<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    private function user(string $role = 'user'): User
    {
        return User::factory()->create(['role' => $role, 'password' => 'password123']);
    }

    private function token(User $user): string
    {
        return $user->createToken('test')->plainTextToken;
    }

    private function bearer(string $token): array
    {
        return ['Authorization' => "Bearer {$token}"];
    }

    public function test_admin_and_regular_user_can_login_safely(): void
    {
        foreach (['admin', 'user'] as $role) {
            $user = $this->user($role);
            $response = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password123'])
                ->assertOk()->assertJsonPath('data.user.role', $role)
                ->assertJsonMissingPath('data.user.password')
                ->assertJsonMissingPath('data.user.remember_token');
            $this->assertNotEmpty($response->json('data.token'));
            $this->assertDatabaseCount('personal_access_tokens', array_search($role, ['admin', 'user'], true) + 1);
        }
    }

    public function test_invalid_credentials_are_generic_and_login_is_validated(): void
    {
        $user = $this->user();
        foreach ([
            ['email' => $user->email, 'password' => 'wrong-password'],
            ['email' => 'unknown@example.test', 'password' => 'wrong-password'],
        ] as $credentials) {
            $this->postJson('/api/login', $credentials)->assertUnauthorized()
                ->assertJsonPath('message', 'Invalid email or password.');
        }
        $this->postJson('/api/login', [])->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_me_requires_and_returns_authenticated_user_without_secrets(): void
    {
        $this->getJson('/api/me')->assertUnauthorized()->assertJsonPath('success', false);
        $user = $this->user();
        $this->withHeaders($this->bearer($this->token($user)))->getJson('/api/me')
            ->assertOk()->assertJsonPath('data.id', $user->id)
            ->assertJsonMissingPath('data.password')->assertJsonMissingPath('data.remember_token')
            ->assertJsonMissingPath('data.token');
    }

    public function test_guest_cannot_access_users(): void
    {
        $this->getJson('/api/users')->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_admin_can_perform_full_crud(): void
    {
        $admin = $this->user('admin');
        $headers = $this->bearer($this->token($admin));
        $target = $this->user();
        $this->withHeaders($headers)->getJson('/api/users')->assertOk();
        $this->withHeaders($headers)->getJson("/api/users/{$target->id}")->assertOk();
        $created = $this->withHeaders($headers)->postJson('/api/users', [
            'name' => 'Created', 'email' => 'admin-created@example.test', 'password' => 'password123',
        ])->assertCreated()->json('data.id');
        $this->withHeaders($headers)->patchJson("/api/users/{$created}", ['name' => 'Updated'])->assertOk();
        $this->withHeaders($headers)->deleteJson("/api/users/{$created}")->assertOk();
    }

    public function test_regular_user_can_read_but_all_writes_are_forbidden(): void
    {
        $user = $this->user('user');
        $target = $this->user();
        $headers = $this->bearer($this->token($user));
        $this->withHeaders($headers)->getJson('/api/users')->assertOk();
        $this->withHeaders($headers)->getJson("/api/users/{$target->id}")->assertOk();
        $payload = ['name' => 'Denied', 'email' => 'denied@example.test', 'password' => 'password123'];
        $this->withHeaders($headers)->postJson('/api/users', $payload)->assertForbidden()->assertJsonPath('message', 'Forbidden.');
        $this->withHeaders($headers)->putJson("/api/users/{$target->id}", ['name' => 'Denied'])->assertForbidden();
        $this->withHeaders($headers)->patchJson("/api/users/{$target->id}", ['name' => 'Denied'])->assertForbidden();
        $this->withHeaders($headers)->deleteJson("/api/users/{$target->id}")->assertForbidden();
    }

    public function test_logout_revokes_only_current_token(): void
    {
        $user = $this->user();
        $token = $this->token($user);
        $headers = $this->bearer($token);
        $this->withHeaders($headers)->postJson('/api/logout')->assertOk();
        $this->refreshApplication();
        $this->withHeaders($headers)->getJson('/api/me')->assertUnauthorized();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_authenticated_regressions_and_missing_user_still_work(): void
    {
        $admin = $this->user('admin');
        $headers = $this->bearer($this->token($admin));
        User::factory()->count(3)->create(['name' => 'Needle']);
        $this->withHeaders($headers)->getJson('/api/users?search=Needle&page=1&per_page=2')
            ->assertOk()->assertJsonPath('meta.total', 3)->assertJsonCount(2, 'data');
        $this->withHeaders($headers)->postJson('/api/users', [])->assertUnprocessable();
        $this->withHeaders($headers)->getJson('/api/users/999999999')->assertNotFound();
    }

    public function test_password_remains_hashed(): void
    {
        $user = $this->user();
        $this->assertNotSame('password123', $user->getRawOriginal('password'));
        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
