<?php

namespace Blrf\Tests\Bookstore\Model;

use Blrf\Tests\Bookstore\TestCase;
use Blrf\Bookstore\Model\Address;
use Blrf\Bookstore\Model\Country;

use function React\Async\await;

class AddressTest extends TestCase
{
    public function testFindByPk()
    {
        $address = await(Address::findByPk(20));
        $this->assertSame(20, $address->getAddressId());
        $this->assertSame('16676', $address->getStreetNumber());
        $this->assertSame('Shelley Street', $address->getStreetName());
        $arr = await($address->toArray());
        /**
         * Country should be set as int
         */
        $this->assertIsIterable($arr);
        $this->assertSame(30, $arr['country']);

        $country = await($address->getCountry());
        $this->assertInstanceOf(Country::class, $country);
        $this->assertSame(30, $country->getCountryId());
        $this->assertSame('Brazil', $country->getCountryName());
    }
}
