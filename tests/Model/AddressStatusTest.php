<?php

namespace Blrf\Tests\Bookstore\Model;

use Blrf\Tests\Bookstore\TestCase;
use Blrf\Bookstore\Model\AddressStatus;
use Blrf\Bookstore\Model\Country;
use Blrf\Orm\Model\Exception\NotFoundException;

use function React\Async\await;

/**
 * AddressStatus model test
 *
 * This is the simplest model.
 */
class AddressStatusTest extends TestCase
{
    public function testFindByPk()
    {
        $status = await(AddressStatus::findByPk(1));
        $this->assertSame(1, $status->getStatusId());
        $this->assertSame('Active', $status->getAddressStatus());
    }

    public function testFindByPkModelNotFound()
    {
        $this->expectException(NotFoundException::class);
        $status = await(AddressStatus::findByPk(3));
    }

    public function testFindWithTrue()
    {
        $statuses = await(AddressStatus::find(true)); // or AddressStatus::findAll()
        $this->assertCount(2, $statuses);
        foreach ($statuses as $status) {
            $this->assertInstanceOf(AddressStatus::class, $status);
        }
    }

    public function testFindAll()
    {
        $statuses = await(AddressStatus::findAll());
        $this->assertCount(2, $statuses);
        foreach ($statuses as $status) {
            $this->assertInstanceOf(AddressStatus::class, $status);
        }
    }
}
