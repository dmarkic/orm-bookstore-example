<?php

declare(strict_types=1);

namespace Blrf\Bookstore\Model;

use Blrf\Orm\Model\Attribute as Attr;
use Blrf\Orm\Model;

/**
 * Customer address
 *
 * This example uses composite primary key. It also defines a "normal" index, which is not really used
 * by ORM, but could be helpful in schema creation.
 * It does not have GeneratedValue.
 *
 * This model links Customer to it's addresses. And each customer address has a status.
 *
 * This is an example of model which cannot use Model::save() at the moment.
 *
 */
#[Attr\Model]
#[Attr\Index(type: 'PRIMARY', fields: ['customer', 'address'])]
class CustomerAddress extends Model
{
    #[Attr\Field(type: 'int', column: 'customer_id')]
    #[Attr\Relation('ONETOONE', Customer::class, 'customer_id')]
    protected Customer $customer;

    #[Attr\Field(type: 'int', column: 'address_id')]
    #[Attr\Relation('ONETOONE', Address::class, 'address_id')]
    protected Address $address;

    #[Attr\Field(type: 'int', column: 'status_id')]
    #[Attr\Relation('ONETOONE', AddressStatus::class, 'status_id')]
    protected AddressStatus $status;
}
