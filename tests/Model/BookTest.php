<?php

namespace Blrf\Tests\Bookstore\Model;

use Blrf\Orm\Factory;
use Blrf\Orm\Model\Exception\NotFoundException;
use Blrf\Tests\Bookstore\TestCase;
use Blrf\Bookstore\Model\Book;
use Blrf\Bookstore\Model\BookLanguage;
use Blrf\Bookstore\Model\Publisher;

use function React\Async\await;

/**
 * Book test
 *
 * Book has ONETOONE relations with BookLanguage and Publisher.
 */
class BookTest extends TestCase
{
    /**
     * Find book by primary key(s)
     */
    public function testFindByPk()
    {
        $book = await(Book::findByPk(10152));

        $manager = Factory::getModelManager();
        $changes = $manager->getChanges($book);
        /**
         * When object is loaded from database, there should be
         * not changes (all changes are S_NEW)
         */
        $this->assertEmpty($changes, 'Should be empty, got: ' . print_r($changes, true));

        $this->assertSame(10152, $book->getBookId());
        $this->assertSame('Shout Out Loud! 2', $book->getTitle());
        $this->assertSame('9781598163179', $book->getIsbn());
        $this->assertSame(194, $book->getNumPages());
        $language = await($book->getLanguage());
        $this->assertInstanceOf(BookLanguage::class, $language);
        $this->assertSame(1, $language->getLanguageId());
        $this->assertSame('eng', $language->getLanguageCode());
        $this->assertSame('English', $language->getLanguageName());
        $publisher = await($book->getPublisher());

        /**
         * Publisher does not have get/set methods, so it calls Manager::getModelField() which is
         * async (returns promise).
         */
        $this->assertInstanceOf(Publisher::class, $publisher);
        $this->assertSame(295, await($publisher->getPublisherId()));
        $this->assertSame('Blu', await($publisher->getPublisherName()));

        $arr = await($book->toArray());

        $this->assertIsIterable($arr);
        $this->assertSame($book->getBookId(), $arr['book_id']);
        $this->assertSame($book->getTitle(), $arr['title']);
        $this->assertSame($book->getIsbn(), $arr['isbn']);
        $this->assertSame($book->getNumPages(), $arr['num_pages']);

        /**
         * As per Hydrator::toArray():
         *
         * Since ONETOONE relation was already resolved (by calling getLanguage() and getPublisher() above)
         * those values will be resolved also into an array.
         *
         * Don't know if that is OK or not, as we did not ask for it.
         * Unless we called $book->toArray(true); // resolve related
         */
        $this->assertIsIterable($arr['language']);
        $this->assertIsIterable($arr['publisher']);
        $changes = $manager->getChanges($book);
        $this->assertEmpty($changes);
    }

    public function testSaveWithoutChanges()
    {
        $book = await(Book::findByPk(10152));
        $ret = await($book->save());
        $this->assertSame($book, $ret);
    }

    public function testUpdateNormalField()
    {
        $title = 'Shout Out Loud! 2';
        $newTitle = 'Shout even louder! 3';
        $book = await(Book::findByPk(10152));
        $this->assertSame($title, $book->getTitle());

        $manager = Factory::getModelManager();
        $changes = $manager->getChanges($book);
        $this->assertEmpty($changes);

        $book->setTitle($newTitle);
        $this->assertSame($newTitle, $book->getTitle());


        $changes = $manager->getChanges($book);
        $this->assertCount(1, $changes);

        $change = reset($changes);
        $this->assertSame('title', $change['field']->name);
        $this->assertSame($title, $change['previous']);
        $this->assertSame($newTitle, $change['current']);

        $ret = await($book->update());
        $this->assertSame($ret, $book);

        $changes = $manager->getChanges($book);
        $this->assertEmpty($changes, 'Changes should be empty after update');

        // fetch it again from database
        $book = await(Book::findByPk(10152));
        $this->assertSame($newTitle, $book->getTitle());

        // set the title back
        $book->setTitle($title);
        $this->assertCount(1, $manager->getChanges($book));
        $ret = await($book->update());
        $this->assertSame($title, $ret->getTitle());
        $this->assertEmpty($manager->getChanges($book));

        // fetch it again from database
        $book = await(Book::findByPk(10152));
        $this->assertSame($title, $book->getTitle());
        $this->assertEmpty($manager->getChanges($book));
    }

    public function testUpdateRelatedFieldWithNormalValue()
    {
        $manager = Factory::getModelManager();
        $book = await(Book::findByPk(10152));
        $changes = $manager->getChanges($book);
        $this->assertEmpty($changes);

        /**
         * Check current publisher
         */
        $publisher = await($book->getPublisher());
        $this->assertSame(295, await($publisher->getPublisherId()));
        $changes = $manager->getChanges($book);
        $this->assertEmpty($changes);

        /**
         * Set to publisher_id: 21 (Ace)
         */
        $ret = await($book->setPublisher(21));
        $this->assertSame($book, $ret);
        $changes = $manager->getChanges($book);
        $this->assertCount(1, $changes);


        /**
         * Check if publisher was updated
         */
        $publisher = await($book->getPublisher());
        $this->assertSame(21, await($publisher->getPublisherId()));
        $changes = $manager->getChanges($book);
        $this->assertCount(1, $changes);

        $ret = await($book->update());
        $this->assertSame($book, $ret);
        $changes = $manager->getChanges($book);
        $this->assertEmpty($changes);
        $publisher = await($book->getPublisher());
        $this->assertSame(21, await($publisher->getPublisherId()));

        /**
         * Reset the publisher back
         */
        $ret = await($book->setPublisher(295));
        $this->assertSame($book, $ret);
        $changes = $manager->getChanges($book);
        $this->assertCount(1, $changes);
        $publisher = await($book->getPublisher());
        $this->assertSame(295, await($publisher->getPublisherId()));

        $ret = await($book->update());
        $this->assertSame($book, $ret);
        $publisher = await($book->getPublisher());
        $this->assertSame(295, await($publisher->getPublisherId()));
        $changes = $manager->getChanges($book);
        $this->assertEmpty($changes);
    }

    public function testUpdateRelatedFieldWithModel()
    {
        $book = await(Book::findByPk(10152));
        /**
         * Check current publisher
         */
        $publisher = await($book->getPublisher());
        $this->assertSame(295, await($publisher->getPublisherId()));

        /**
         * Load new publisher: Ace (21)
         */
        $publisher = await(Publisher::findByPk(21));
        $ret = await($book->setPublisher($publisher));
        $this->assertSame($book, $ret);

        /**
         * Check if publisher was updated
         */
        $publisher = await($book->getPublisher());
        $this->assertSame(21, await($publisher->getPublisherId()));

        /**
         * Reset the publisher back
         */
        $ret = await($book->setPublisher(295));
        $this->assertSame($book, $ret);
        $publisher = await($book->getPublisher());
        $this->assertSame(295, await($publisher->getPublisherId()));
    }

    public function testFindFirstBy()
    {
        $book = await(Book::findFirstBy(['title' => 'Shout Out Loud! 2']));
        $this->assertInstanceOf(Book::class, $book);
        $this->assertSame(10152, $book->getBookId());
    }

    public function testJsonSerialize()
    {
        $book = await(Book::findByPk(10152));
        $json = json_encode($book);
        $this->assertEquals(
            '{"book_id":10152,"title":"Shout Out Loud! 2","isbn":"9781598163179","num_pages":194,' .
            '"publication_date":{"date":"2006-08-01 00:00:00.000000","timezone_type":3,"timezone":"UTC"},' .
            '"language":1,"publisher":295}',
            $json
        );
    }

    public function testAssignInsertUpdateAndDelete()
    {
        $book = new Book();
        $data = [
            'title'             => 'Learn Blrf Orm',
            'isbn'              => '9999999999999',
            'num_pages'         => 10,
            'publication_date'  => '2024-03-09',
            'language'          => 17,
            'publisher'         => 1381
        ];
        await($book->assign($data));
        $this->assertSame('Learn Blrf Orm', $book->getTitle());
        $this->assertSame('9999999999999', $book->getIsbn());
        $this->assertSame(10, $book->getNumPages());
        $this->assertSame('2024-03-09', ($book->getPublicationDate()->format('Y-m-d')));
        $this->assertSame(17, await($book->getLanguage())->getLanguageId());
        // double await here, as publisher does not have getPublisherId() method
        $this->assertSame(1381, await(await($book->getPublisher())->getPublisherId()));

        /**
         * Insert book
         */
        $ret = await($book->insert());
        $bookId = $book->getBookId();
        $this->assertSame($book, $ret);
        $this->assertGreaterThan(0, $bookId);
        $this->assertSame('Learn Blrf Orm', $book->getTitle());
        $this->assertSame('9999999999999', $book->getIsbn());
        $this->assertSame(10, $book->getNumPages());
        $this->assertSame('2024-03-09', $book->getPublicationDate()->format('Y-m-d'));
        $this->assertSame(17, await($book->getLanguage())->getLanguageId());
        // double await here, as publisher does not have getPublisherId() method
        $this->assertSame(1381, await(await($book->getPublisher())->getPublisherId()));

        /**
         * Update title
         */
        $manager = Factory::getModelManager();
        $book->setTitle('Best seller: Learn Blrf Orm');
        $this->assertCount(1, $manager->getChanges($book));

        $ret = await($book->update());
        $this->assertSame($book, $ret);
        $this->assertEmpty($manager->getChanges($book));
        $this->assertGreaterThan(0, $book->getBookId());
        $this->assertSame('Best seller: Learn Blrf Orm', $book->getTitle());
        $this->assertSame('9999999999999', $book->getIsbn());
        $this->assertSame(10, $book->getNumPages());
        $this->assertSame('2024-03-09', $book->getPublicationDate()->format('Y-m-d'));
        $this->assertSame(17, await($book->getLanguage())->getLanguageId());
        // double await here, as publisher does not have getPublisherId() method
        $this->assertSame(1381, await(await($book->getPublisher())->getPublisherId()));

        /**
         * Delete model
         *
         * But, first make a change, so we see that changes disappear.
         */
        $book->setTitle('Before delete');
        $this->assertCount(1, $manager->getChanges($book));
        $this->assertTrue(await($book->delete()));
        $this->assertEmpty($manager->getChanges($book));

        $exception = null;
        try {
            await(Book::findByPk($bookId));
        } catch (\Throwable $e) {
            $exception = $e;
        }
        $this->assertInstanceOf(NotFoundException::class, $exception);
    }

    public function testAssignInsertWithSaveUpdateWithSaveAndDelete()
    {
        $book = new Book();
        $data = [
            'title'             => 'Learn Blrf Orm',
            'isbn'              => '9999999999999',
            'num_pages'         => 10,
            'publication_date'  => '2024-03-09',
            'language'          => 17,
            'publisher'         => 1381
        ];
        await($book->assign($data));
        $this->assertSame('Learn Blrf Orm', $book->getTitle());
        $this->assertSame('9999999999999', $book->getIsbn());
        $this->assertSame(10, $book->getNumPages());
        $this->assertSame('2024-03-09', $book->getPublicationDate()->format('Y-m-d'));
        $this->assertSame(17, await($book->getLanguage())->getLanguageId());
        // double await here, as publisher does not have getPublisherId() method
        $this->assertSame(1381, await(await($book->getPublisher())->getPublisherId()));

        /**
         * Insert book
         */
        $ret = await($book->save());
        $bookId = $book->getBookId();
        $this->assertSame($book, $ret);
        $this->assertGreaterThan(0, $bookId);
        $this->assertSame('Learn Blrf Orm', $book->getTitle());
        $this->assertSame('9999999999999', $book->getIsbn());
        $this->assertSame(10, $book->getNumPages());
        $this->assertSame('2024-03-09', $book->getPublicationDate()->format('Y-m-d'));
        $this->assertSame(17, await($book->getLanguage())->getLanguageId());
        // double await here, as publisher does not have getPublisherId() method
        $this->assertSame(1381, await(await($book->getPublisher())->getPublisherId()));

        /**
         * Update title
         */
        $manager = Factory::getModelManager();
        $book->setTitle('Best seller: Learn Blrf Orm');
        $this->assertCount(1, $manager->getChanges($book));

        $ret = await($book->save());
        $this->assertSame($book, $ret);
        $this->assertEmpty($manager->getChanges($book));
        $this->assertGreaterThan(0, $book->getBookId());
        $this->assertSame('Best seller: Learn Blrf Orm', $book->getTitle());
        $this->assertSame('9999999999999', $book->getIsbn());
        $this->assertSame(10, $book->getNumPages());
        $this->assertSame('2024-03-09', $book->getPublicationDate()->format('Y-m-d'));
        $this->assertSame(17, await($book->getLanguage())->getLanguageId());
        // double await here, as publisher does not have getPublisherId() method
        $this->assertSame(1381, await(await($book->getPublisher())->getPublisherId()));

        /**
         * Delete model
         *
         * But, first make a change, so we see that changes disappear.
         */
        $book->setTitle('Before delete');
        $this->assertCount(1, $manager->getChanges($book));
        $this->assertTrue(await($book->delete()));
        $this->assertEmpty($manager->getChanges($book));

        $exception = null;
        try {
            await(Book::findByPk($bookId));
        } catch (\Throwable $e) {
            $exception = $e;
        }
        $this->assertInstanceOf(NotFoundException::class, $exception);
    }
}
