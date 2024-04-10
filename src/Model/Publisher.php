<?php

declare(strict_types=1);

namespace Blrf\Bookstore\Model;

use Blrf\Bookstore\ActiveEnum;
use Blrf\Orm\Model\Attribute as Attr;
use Blrf\Orm\Model;

/**
 * Example model that is described with attributes
 *
 * Model attribute or Source should be specified to enable Attribute meta data driver.
 *
 * Here we specify only Attr\Model attribute, so Source is figured out from class name.
 *
 * This model has no setter and getters method, so calls to getPublisherId() are async and return
 * model property value.
 *
 * This model also showcases a support for ENUM's via PHP8 Backed Enums with publisher_active field.
 * @see Blrf\Bookstore\ActiveEnum
 */
#[Attr\Model]
#[Attr\Index(type: 'PRIMARY', fields: ['publisher_id'])]
class Publisher extends Model
{
    #[Attr\Field]
    #[Attr\GeneratedValue]
    /**
     * This will create virtual 'Books' field that can be accessed via $publisher->getBooks()
     */
    #[Attr\Relation('ONETOMANY', Book::class, 'publisher', 'Books')]
    protected int $publisher_id;

    #[Attr\Field(type: ['type' => 'string', 'max' => 400])]
    protected string $publisher_name;

    #[Attr\Field]
    protected ActiveEnum $publisher_active;
}
