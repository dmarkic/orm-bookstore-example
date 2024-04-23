<?php

namespace Blrf\Tests\Bookstore\Model;

use Blrf\Tests\Bookstore\TestCase;
use Blrf\Bookstore\Model\Interval;
use Blrf\Orm\Model\Exception\NotFoundException;

use function React\Async\await;

/**
 * Interval model test
 *
 * This model uses QuoteIdentifier attribute and quoteIdentifier arguments to source to support
 * reserved database keywords as identifiers.
 */
class IntervalTest extends TestCase
{
    public function testFindByPk(): void
    {
        $interval = await(Interval::findByPk(1));
        $this->assertSame(1, $interval->getIntervalId());
        $this->assertSame('Interval#1', $interval->getInterval());
    }

    public function testFindFirstByInterval(): void
    {
        $interval = await(Interval::findFirstBy(['interval' => 'Interval#2']));
        $this->assertSame(2, $interval->getIntervalId());
    }

    public function testUpdate(): void
    {
        $interval = await(Interval::findByPk(3));
        $oldInterval = $interval->getInterval();
        $interval->setInterval('Updated#3');
        await($interval->save());

        $interval = await(Interval::findByPk(3));
        $this->assertSame('Updated#3', $interval->getInterval());

        // update back
        $interval->setInterval($oldInterval);
        $interval = await($interval->save());

        // check
        $interval = await(Interval::findByPk(3));
        $this->assertSame($oldInterval, $interval->getInterval());
    }

    public function testInsertAndDelete()
    {
        $interval = new Interval();
        $interval->setInterval('Inserted#5');
        $interval = await($interval->save());
        $this->assertSame(5, $interval->getIntervalId());
        $this->assertSame('Inserted#5', $interval->getInterval());

        await($interval->delete());

        try {
            await(Interval::findByPk(5));
        } catch (\Throwable $e) {
            $this->assertInstanceOf(NotFoundException::class, $e);
        }
    }
}
