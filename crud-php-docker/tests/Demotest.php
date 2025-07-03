<?php

use PHPUnit\Framework\TestCase;
use App\Demo;

class DemoTest extends TestCase {
    public function testTrue() {
        $demo = new Demo();
        $this->assertTrue($demo->alwaysTrue());
    }

    public function testFalse() {
        $demo = new Demo();
        $this->assertFalse($demo->alwaysFalse());
    }

    public function testNumber() {
        $demo = new Demo();
        $this->assertEquals(42, $demo->getNumber());
    }
}
