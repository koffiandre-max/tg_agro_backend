<?php

namespace Modules\SendEmail\Tests;

class SendEmailModuleTest extends \ModuleTestCase
{
    protected string $featureCode = 'send_email';

    public function test_dashboard_is_accessible(): void
    {
        $this->actingAsUser()
            ->get(route('admin.send_email.index'))
            ->assertOk();
    }

    public function test_feature_gate_blocks_without_activation(): void
    {
        $this->assertRouteRequiresFeature('admin.send_email.index', 'send_email');
    }
}
