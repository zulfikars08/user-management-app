<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->make(['role' => 'admin']));
    }

    private function user(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    public function test_lists_users_without_sensitive_fields(): void
    {
        $user = $this->user(['remember_token' => 'secret-token']);

        $this->getJson('/api/users')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.id', $user->id)
            ->assertJsonMissingPath('data.0.password')
            ->assertJsonMissingPath('data.0.remember_token');
    }

    public function test_shows_user(): void
    {
        $user = $this->user();

        $this->getJson("/api/users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_creates_valid_user_with_hashed_password_and_default_role(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'Created User',
            'email' => 'created@example.test',
            'password' => 'plain-password',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.role', 'user')
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.remember_token');

        $user = User::where('email', 'created@example.test')->firstOrFail();
        $this->assertNotSame('plain-password', $user->getRawOriginal('password'));
        $this->assertTrue(Hash::check('plain-password', $user->password));
    }

    public function test_create_rejects_invalid_data(): void
    {
        $this->postJson('/api/users', ['name' => '', 'email' => 'invalid', 'password' => 'short'])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_create_rejects_duplicate_email(): void
    {
        $user = $this->user();

        $this->postJson('/api/users', [
            'name' => 'Duplicate', 'email' => $user->email, 'password' => 'password123',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_updates_user(): void
    {
        $user = $this->user();

        $this->patchJson("/api/users/{$user->id}", ['name' => 'Updated', 'role' => 'admin'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated')
            ->assertJsonPath('data.role', 'admin');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated', 'role' => 'admin']);
    }

    public function test_update_with_same_email_succeeds(): void
    {
        $user = $this->user();

        $this->putJson("/api/users/{$user->id}", ['email' => $user->email])
            ->assertOk()->assertJsonPath('data.email', $user->email);
    }

    public function test_update_rejects_another_users_email(): void
    {
        $user = $this->user();
        $other = $this->user();

        $this->patchJson("/api/users/{$user->id}", ['email' => $other->email])
            ->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_deletes_user(): void
    {
        $user = $this->user();

        $this->deleteJson("/api/users/{$user->id}")
            ->assertOk()->assertJsonPath('data', null);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_missing_user_returns_json_404(): void
    {
        $this->getJson('/api/users/999999999')
            ->assertNotFound()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Resource not found.');
    }

    public function test_searches_by_name(): void
    {
        $match = $this->user(['name' => 'Zulfikar Search']);
        $this->user(['name' => 'Other Person']);

        $this->getJson('/api/users?search=Zulfikar')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $match->id);
    }

    public function test_searches_by_numeric_id(): void
    {
        $match = $this->user(['name' => 'Numeric Match']);
        $this->user();

        $this->getJson("/api/users?search={$match->id}")
            ->assertOk()->assertJsonFragment(['id' => $match->id]);
    }

    public function test_paginates_at_database_level(): void
    {
        User::factory()->count(12)->create();

        $this->getJson('/api/users?page=2&per_page=5')
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 12)
            ->assertJsonPath('meta.last_page', 3);
    }

    public function test_search_and_pagination_work_together(): void
    {
        User::factory()->count(6)->create(['name' => 'Needle User']);
        $this->user(['name' => 'Excluded']);

        $this->getJson('/api/users?search=Needle&page=2&per_page=2')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.total', 6);
    }

    public function test_rejects_invalid_role_and_excessive_page_size(): void
    {
        $this->postJson('/api/users', [
            'name' => 'Bad Role', 'email' => 'bad-role@example.test',
            'password' => 'password123', 'role' => 'superuser',
        ])->assertUnprocessable()->assertJsonValidationErrors('role');

        $this->getJson('/api/users?per_page=101')
            ->assertUnprocessable()->assertJsonValidationErrors('per_page');
    }

    public function test_unexpected_api_errors_use_safe_json_500(): void
    {
        Route::get('/api/test-unexpected-error', fn () => throw new \RuntimeException('internal-sensitive-detail'));

        $this->getJson('/api/test-unexpected-error')
            ->assertInternalServerError()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'An unexpected error occurred.')
            ->assertJsonMissing(['internal-sensitive-detail']);
    }
}
