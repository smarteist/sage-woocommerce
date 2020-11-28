<?php

namespace Hexbit\Woocommerce\Tests;

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{

    /**
     * Prepares the test environment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }


    /**
     * Cleans up the test environment after each test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}