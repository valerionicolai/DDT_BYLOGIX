<?php

namespace Tests\Feature;

use Tests\TestCase;

class SessionConfigTest extends TestCase
{
    public function test_session_configuration()
    {
        $this->assertEquals('array', config('session.driver'));
        $this->assertEquals('localhost', config('session.domain'));
        $this->assertTrue(config('session.secure'));
        $this->assertEquals('lax', config('session.same_site'));
        $this->assertContains('localhost', config('sanctum.stateful'));
    }
}