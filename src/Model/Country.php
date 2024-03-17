<?php

declare(strict_types=1);

namespace Blrf\Bookstore\Model;

use Blrf\Orm\Model\Attribute as Attr;
use Blrf\Orm\Model;

/**
 * Country model
 */
#[Attr\Model]
#[Attr\Index(type: 'PRIMARY', fields: ['country_id'])]
class Country extends Model
{
    #[Attr\Field]
    #[Attr\GeneratedValue]
    protected int $country_id;

    #[Attr\Field(type: ['type' => 'string', 'max' => 200])]
    protected string $country_name;

    public function getCountryId(): int
    {
        return $this->country_id;
    }

    public function getCountryName(): string
    {
        return $this->country_name;
    }
}
