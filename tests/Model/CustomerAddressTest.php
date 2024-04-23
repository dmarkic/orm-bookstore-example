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

    public function testChangeStatus()
    {
        // load and check that status is Inactive
        $caddress = await(CustomerAddress::findByPk(3, 128));
        $status = await($caddress->getStatus());
        $this->assertSame('Inactive', $status->getAddressStatus());

        // find activeStatus
        $activeStatus = await(AddressStatus::findByPk(1));
        $this->assertSame('Active', $activeStatus->getAddressStatus());

        // update address to Active status
        await($caddress->setStatus($activeStatus));
        $status = await($caddress->getStatus());
        $this->assertSame('Active', $status->getAddressStatus());
        await($caddress->update());

        // check if update was successful
        $caddress = await(CustomerAddress::findByPk(3, 128));
        $status = await($caddress->getStatus());
        $this->assertSame('Active', $status->getAddressStatus());

        // revert back
        // find inactive status
        $inactiveStatus = await(AddressStatus::findByPk(2));
        $this->assertSame('Inactive', $inactiveStatus->getAddressStatus());

        // update address to Inactive status
        await($caddress->setStatus($inactiveStatus));
        $status = await($caddress->getStatus());
        $this->assertSame('Inactive', $status->getAddressStatus());
        await($caddress->update());

        // check if update was successful
        $caddress = await(CustomerAddress::findByPk(3, 128));
        $status = await($caddress->getStatus());
        $this->assertSame('Inactive', $status->getAddressStatus());
    }

    public function testFindWithJoin()
    {
        $promise = CustomerAddress::find()->then(
            function ($qb) {
                return $qb
                    ->join(
                        'address_status',
                        'address_status.status_id = customer_address.status_id'
                    )
                    ->where(
                        fn($cb) => $cb->and(
                            $cb->eq('customer_id'),
                            $cb->eq('address_status')
                        )
                    )
                    ->addParameter(3)
                    ->addParameter('Inactive')
                    ->execute();
            }
        );
        $addresses = await($promise);
        $this->assertCount(1, $addresses);
    }
}
