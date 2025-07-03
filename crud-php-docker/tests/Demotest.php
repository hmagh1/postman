<?php

use PHPUnit\Framework\TestCase;
use App\Demo;

class DemoTest extends TestCase {
    public function testHello() {
        $demo = new Demo();
        $this->assertEquals("hello", $demo->hello());
    }
}
