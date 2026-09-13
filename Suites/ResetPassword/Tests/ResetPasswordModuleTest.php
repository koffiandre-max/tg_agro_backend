<?php

namespace Modules\ResetPassword\Tests;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Modules\ResetPassword\Mail\ResetPasswordMail;

class ResetPasswordModuleTest extends \ModuleTestCase
{
    public function test_request_form_is_accessible_without_auth(): void
    {
        $this->get(route('reset_password.request'))->assertOk();
    }

    public function test_reset_form_requires_token(): void
    {
        $this->get(route('reset_password.reset', ['token' => 'abc123', 'email' => 'test@example.com']))
            ->assertOk();
    }

    public function test_send_reset_link_sends_email(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'client@example.com']);

        $this->post(route('reset_password.email'), ['email' => 'client@example.com'])
            ->assertSessionHas('status');

        Mail::assertSent(ResetPasswordMail::class);

        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'client@example.com']);
    }

    public function test_send_reset_link_does_not_leak_invalid_email(): void
    {
        Mail::fake();

        $this->post(route('reset_password.email'), ['email' => 'inexistant@example.com'])
            ->assertSessionHas('status');

        Mail::assertNothingSent();
    }

    public function test_reset_password_updates_credentials(): void
    {
        $user = User::factory()->create(['email' => 'client@example.com']);

        DB::table('password_reset_tokens')->insert([
            'email'      => 'client@example.com',
            'token'      => Hash::make('valid-token'),
            'created_at' => now(),
        ]);

        $this->post(route('reset_password.store'), [
            'email'                         => 'client@example.com',
            'token'                         => 'valid-token',
            'new_password'                  => 'nouveau-secret-123',
            'new_password_confirmation'     => 'nouveau-secret-123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('nouveau-secret-123', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'client@example.com']);
    }

    public function test_reset_rejects_invalid_token(): void
    {
        User::factory()->create(['email' => 'client@example.com']);

        $this->post(route('reset_password.store'), [
            'email'                         => 'client@example.com',
            'token'                         => 'mauvais-token',
            'new_password'                  => 'nouveau-secret-123',
            'new_password_confirmation'     => 'nouveau-secret-123',
        ])->assertSessionHasErrors('email');
    }
}


