<?php

namespace Blrf\Tests\Bookstore\Model;

use Blrf\Tests\Bookstore\TestCase;
use Blrf\Bookstore\Model\ShippingMethod;

use function React\Async\await;

class ShippingMethodTest extends TestCase
{
    public function testFindByPk()
    {
        $s = await(ShippingMethod::findByPk(1));
        $this->assertInstanceOf(ShippingMethod\Standard::class, $s);
        $this->assertSame('Shipping standard', $s->ship());
        $this->assertSame(5.9, $s->getCost());
    }
}
