<?php

namespace Blrf\Tests\Bookstore\Model;

use Blrf\Tests\Bookstore\TestCase;
use Blrf\Bookstore\Model\Customer;
use Blrf\Bookstore\Model\CustomerAddress;
use Blrf\Bookstore\Model\AddressStatus;

use function React\Async\await;

/**
 * Customer address test
 *
 * This model uses composite primary index, so findByPk requires two arguments.
 */
class CustomerAddressTest extends TestCase
{
    public function testFindByPk()
    {
        $caddress = await(CustomerAddress::findByPk(64, 62));
        $customer = await($caddress->getCustomer());
        $this->assertSame(64, $customer->getCustomerId());
        $address = await($caddress->getAddress());
        $this->assertSame(62, $address->getAddressId());
        $status = await($caddress->getStatus());
        $this->assertSame('Active', $status->getAddressStatus());
    }
}
