<?php

declare(strict_types=1);

namespace Blrf\Bookstore\Model;

use DateTimeInterface;
use Blrf\Orm\Model;
use Blrf\Orm\Model\Meta\Data as MetaData;
use Blrf\Orm\Model\Attribute as Attr;

/**
 * Example model where meta data is received from ormMetaData() method.
 *
 * @todo How to access the language_id field without loading whole related object?
 */
class Book extends Model
{
    protected ?int $book_id = null;
    protected ?string $title = null;
    protected ?string $isbn = null;
    protected ?int $num_pages = null;
    protected ?DateTimeInterface $publication_date = null;
    protected BookLanguage $language;
    protected Publisher $publisher;

    public static function ormMetaData(MetaData $data): MetaData
    {
        return $data
            ->setSource(new Attr\Source('book'))
            ->addField(new Attr\Field('book_id', new Attr\Field\TypeInt(), null, new Attr\GeneratedValue()))
            ->createField('title', ['type' => 'string', 'min' => 0, 'max' => 400]) // same as addField, but shorter
            ->createField('isbn', new Attr\Field\TypeString(0, 13), 'isbn13') // map isbn to isbn13 column
            ->createField('num_pages', ['type' => 'int', 'min' => 0])
            ->createField('publication_date', ['type' => 'date'])
            // BookLanguage related field (magic method: getLanguage())
            ->createField(
                'language', // model prop
                'int', // database value
                'language_id', // model column in database
                // relation: ONETOONE BookLanguage field: language_id
                new Attr\Relation('ONETOONE', BookLanguage::class, 'language_id')
            )
            // Publisher related field (magic method: getPublisher())
            ->createField(
                'publisher', // model prop
                'int', // database value
                'publisher_id', // model column in database
                // relation: ONETOONE BookLanguage field: language_id
                new Attr\Relation('ONETOONE', Publisher::class, 'publisher_id')
            )
            // define primary index
            ->createIndex(Attr\Index\Type::PRIMARY, ['book_id'], 'PRIMARY KEY');
    }

    public function getBookId(): ?int
    {
        return $this->book_id;
    }

    public function setTitle(string $title = null): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setIsbn(string $isbn = null): self
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setNumPages(int $pages): self
    {
        $this->num_pages = $pages;
        return $this;
    }

    public function getNumPages(): ?int
    {
        return $this->num_pages;
    }

    public function setPublicationDate(DateTimeInterface $date): self
    {
        $this->publication_Date = $date;
        return $this;
    }

    public function getPublicationDate(): ?DateTimeInterface
    {
        return $this->publication_date;
    }
}
