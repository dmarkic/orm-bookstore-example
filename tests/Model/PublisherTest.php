<?php

namespace Blrf\Tests\Bookstore\Model;

use Blrf\Tests\Bookstore\TestCase;
use Blrf\Bookstore\Model\Publisher;

use function React\Async\await;

/**
 * Publisher test
 *
 * Publisher has ONETOMANY relation to Book. One publisher may have many books.
 * It's a RELATED field type which is virtual.
 *
 * Publisher has no getters, so all calls to get*() methods are forwarded to Manager::getModelField()
 * which is async and returns promise.
 */
class PublisherTest extends TestCase
{
    public function testGetBooks()
    {
        $publisher = await(Publisher::findByPk(960));
        $this->assertInstanceOf(Publisher::class, $publisher);
        $this->assertSame(960, await($publisher->getPublisherId()));
        $this->assertSame('HighBridge Company', await($publisher->getPublisherName()));

        $books = await($publisher->getBooks());
        $this->assertSame(2, count($books));

        $expIds = [10164, 10163];

        foreach ($books as $book) {
            $this->assertContains($book->getBookId(), $expIds);
        }

        $arr = await($publisher->toArray());
        $this->assertCount(2, $arr);
        $this->assertSame(await($publisher->getPublisherId()), $arr['publisher_id']);
        $this->assertSame(await($publisher->getPublisherName()), $arr['publisher_name']);
    }
}
