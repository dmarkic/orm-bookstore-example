<?php

namespace Blrf\Tests\Bookstore\Model;

use Blrf\Tests\Bookstore\TestCase;
use Blrf\Bookstore\Model\Customer;
use Blrf\Bookstore\Model\CustomerAddress;
use Blrf\Bookstore\Model\AddressStatus;

use function React\Async\await;

/**
 * Customer model test
 *
 * Interesting thing to test here is: Find all customers whose addresses are not active.
 *
 * This model uses ONETOMANY relation on customer_id field to CustomerAddress which is then additionaly linked
 * as ONETOONE to Address.
 */
class CustomerTest extends TestCase
{
    public function testFindByPk()
    {
        $customer = await(Customer::findByPk(64));
        $this->assertSame(64, $customer->getCustomerId());
        $this->assertSame('Zia', $customer->getFirstName());
        $this->assertSame('Roizin', $customer->getLastName());
        $this->assertSame('zroizin1r@cnbc.com', $customer->getEmail());
    }

    public function testGetAddresses()
    {
        $customer = await(Customer::findByPk(64));
        $addresses = await($customer->getAddresses());
        $this->assertCount(2, $addresses);
        $ids = [];
        foreach ($addresses as $address) {
            $this->assertInstanceOf(CustomerAddress::class, $address);
            $ids[] = await($address->getAddress())->getAddressId();
            $status = await($address->getStatus());
            $this->assertInstanceOf(AddressStatus::class, $status);
            $this->assertSame('Active', $status->getAddressStatus());
        }
        $this->assertContains(62, $ids);
        $this->assertContains(496, $ids);
    }

    public function testGetAddressesWithLimitArgument()
    {
        $customer = await(Customer::findByPk(64));
        $addresses = await($customer->getAddresses(['limit' => 1]));
        $this->assertCount(1, $addresses);
    }
}
