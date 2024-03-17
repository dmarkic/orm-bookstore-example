<?php

declare(strict_types=1);

namespace Blrf\Bookstore\Model;

use Blrf\Orm\Model\Attribute as Attr;
use Blrf\Orm\Model;
use React\Promise\PromiseInterface;

/**
 * Address model
 *
 * This example creates ONETOONE relation with Attr\Relation for Country.
 *
 */
#[Attr\Model]
#[Attr\Index(type: 'PRIMARY', fields: ['address_id'])]
class Address extends Model
{
    #[Attr\Field]
    #[Attr\GeneratedValue]
    protected int $address_id;

    #[Attr\Field(type: ['type' => 'string', 'max' => 10])]
    protected string $street_number;

    #[Attr\Field(type: ['type' => 'string', 'max' => 200])]
    protected string $street_name;

    #[Attr\Field(type: ['type' => 'string', 'max' => 100])]
    protected string $city;

    /**
     * We need to specify the column type and name, as we create relation
     * directly into Country model.
     *
     * Two magic methods created: getCountry() and setCountry().
     */
    #[Attr\Field(type: 'int', column: 'country_id')]
    #[Attr\Relation('ONETOONE', Country::class, 'country_id')]
    protected Country $country;

    public function getAddressId(): int
    {
        return $this->address_id;
    }

    public function getStreetNumber(): string
    {
        return $this->street_number;
    }

    public function getStreetName(): string
    {
        return $this->street_name;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Get full address
     *
     * We need to return promise as we have to obtain related country name.
     *
     * @return PromiseInterface<string>
     */
    public function getFullAddress(): PromiseInterface
    {
        return $this->getCountry()->then(
            function (Country $country): string {
                return $this->street_name . ' ' . $this->street_number . "\n" .
                       $this->city . "\n" .
                       $country->getCountryName();
            }
        );
    }
}
