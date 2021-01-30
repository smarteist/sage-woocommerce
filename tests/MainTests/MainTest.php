<?php


namespace Hexbit\Woocommerce\Tests\MainTests;

use Hexbit\Woocommerce\Tests\BaseTestCase;

class MainTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
    */
    public function testAssertTrueIsTrue()
    {
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}