<?php

namespace Modules\Payment\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Modules\Payment\Models\Payment;
use Modules\Payment\Models\Plan;
use Modules\Payment\Models\Subscription;
use Modules\Payment\Services\SubscriptionService;

class PaymentModuleTest extends \ModuleTestCase
{
    use DatabaseTransactions;

    protected string $featureCode = 'payment';

    /**
     * Crée un utilisateur hôte minimal et le connecte.
     */
    protected function loginUser(): \App\Models\User
    {
        $id = DB::table('users')->insertGetId([
            'name' => 'Test Payment',
            'email' => 'payment-test-'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return \App\Models\User::query()->findOrFail($id);
    }

    public function test_default_plans_are_seeded(): void
    {
        $this->assertDatabaseHas('suite_plans', ['code' => 'pro_monthly']);
        $this->assertDatabaseHas('suite_plans', ['code' => 'entreprise_yearly']);
    }

    public function test_subscribe_to_paid_plan_creates_pending_payment(): void
    {
        $user = $this->loginUser();
        $plan = Plan::query()->where('code', 'pro_monthly')->firstOrFail();

        $this->actingAs($user)
            ->post(route('admin.payment.subscribe', $plan), ['method' => 'card'])
            ->assertRedirect();

        $this->assertDatabaseHas('suite_subscriptions', [
            'business_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_PENDING,
        ]);

        $this->assertDatabaseHas('suite_payments', [
            'business_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Payment::STATUS_PENDING,
            'method' => Payment::METHOD_CARD,
        ]);
    }

    public function test_free_plan_activates_immediately(): void
    {
        $user = $this->loginUser();
        $plan = Plan::query()->where('code', 'gratuit_monthly')->firstOrFail();

        $this->actingAs($user)
            ->post(route('admin.payment.subscribe', $plan), ['method' => 'card'])
            ->assertRedirect();

        $this->assertDatabaseHas('suite_subscriptions', [
            'business_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
        ]);
    }

    public function test_mark_paid_activates_subscription_for_full_period(): void
    {
        $user = $this->loginUser();
        $plan = Plan::query()->where('code', 'pro_monthly')->firstOrFail();

        $service = app(SubscriptionService::class);
        $subscription = $service->subscribe($user->id, $user->id, $plan, ['method' => 'card']);
        $payment = $service->createPayment($subscription);

        $service->markPaid($payment);

        $subscription->refresh();
        $this->assertSame(Subscription::STATUS_ACTIVE, $subscription->status);
        $this->assertTrue($subscription->ends_at->isFuture());
        $this->assertTrue($payment->refresh()->isSucceeded());
    }

    public function test_auto_renew_command_renews_due_subscriptions(): void
    {
        $user = $this->loginUser();
        $plan = Plan::query()->where('code', 'pro_monthly')->firstOrFail();

        $service = app(SubscriptionService::class);
        $subscription = $service->subscribe($user->id, $user->id, $plan, ['method' => 'card']);
        $service->markPaid($service->createPayment($subscription));
        $subscription->refresh();

        // Échéance atteinte + renouvellement automatique activé.
        $subscription->forceFill([
            'auto_renew' => true,
            'next_payment_at' => now()->subDay()->toDateString(),
        ])->save();

        $endsBefore = $subscription->ends_at->toDateString();

        $this->artisan('payment:process-auto-renewals')->assertSuccessful();

        $subscription->refresh();
        $this->assertTrue($subscription->ends_at->gt($endsBefore));
        $this->assertDatabaseHas('suite_payments', [
            'subscription_id' => $subscription->id,
            'is_renewal' => true,
            'status' => Payment::STATUS_SUCCEEDED,
        ]);
    }

    public function test_cancel_stops_auto_renew_but_keeps_active_period(): void
    {
        $user = $this->loginUser();
        $plan = Plan::query()->where('code', 'pro_monthly')->firstOrFail();

        $service = app(SubscriptionService::class);
        $subscription = $service->subscribe($user->id, $user->id, $plan, ['method' => 'card']);
        $service->markPaid($service->createPayment($subscription));

        $service->cancel($subscription);

        $subscription->refresh();
        $this->assertFalse($subscription->auto_renew);
        $this->assertSame(Subscription::STATUS_ACTIVE, $subscription->status);
        $this->assertTrue($subscription->isActive());
    }
}

