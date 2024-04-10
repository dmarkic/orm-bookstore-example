<?php

declare(strict_types=1);

namespace Blrf\Bookstore\Model;

use Blrf\Orm\Model\Attribute as Attr;
use Blrf\Orm\Model;

/**
 * Customer model
 *
 * This example uses Attr\AutoIncrement which is an alias for Attr\GeneratedValue
 *
 * Customer may have many addresses. It's related via CustomerAddress model, which holds the address status and
 * is further related to Address.
 */
#[Attr\Model]
#[Attr\Index(type: 'PRIMARY', fields: ['customer_id'])]
class Customer extends Model
{
    #[Attr\Field]
    #[Attr\AutoIncrement]
    #[Attr\Relation('ONETOMANY', CustomerAddress::class, 'customer', 'Addresses')]
    protected int $customer_id;

    #[Attr\Field(type: ['type' => 'string', 'max' => 200])]
    protected string $first_name;

    #[Attr\Field(type: ['type' => 'string', 'max' => 200])]
    protected string $last_name;

    #[Attr\Field(type: ['type' => 'string', 'max' => 350])]
    protected string $email;

    public function getCustomerId(): int
    {
        return $this->customer_id;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
