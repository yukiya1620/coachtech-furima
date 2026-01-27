<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** ID1 会員登録 */
    public function test_register_validation_errors(): void
    {
        $res = $this->post(route('register'), [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $res->assertStatus(302);
        $res->assertSessionHasErrors(['name', 'email', 'password']);
    }

    /** ID1 会員登録 */
    public function test_register_success_creates_user(): void
    {
        $res = $this->post(route('register'), [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $res->assertStatus(302);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /** ID2 ログイン：必須バリデーション */
    public function test_login_validation_errors(): void
    {
        $res = $this->post(route('login'), [
            'email' => '',
            'password' => '',
        ]);

        $res->assertStatus(302);
        $res->assertSessionHasErrors(['email', 'password']);
    }

    /** ID2 ログイン：誤入力で失敗 */
    public function test_login_fails_with_wrong_credentials(): void
    {
        User::factory()->create([
            'email' => 'a@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $res = $this->post(route('login'), [
            'email' => 'a@example.com',
            'password' => 'wrong',
        ]);

        $res->assertStatus(302);

        $this->assertGuest();
    }

    /** ID2 ログイン：正しい情報で成功 */
    public function test_login_success(): void
    {
        $user = User::factory()->create([
            'email' => 'a@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $res = $this->post(route('login'), [
            'email' => 'a@example.com',
            'password' => 'password123',
        ]);

        $res->assertStatus(302);
        $this->assertAuthenticatedAs($user);
    }

    /** ID3 ログアウト：ログアウトできる */
    public function test_logout(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user);

        $res = $this->post(route('logout'));
        $res->assertStatus(302);
        $this->assertGuest();
    }

    /** ID16 メール認証 */
    public function test_email_verification_notification_is_sent(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
