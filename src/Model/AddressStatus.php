<?php

declare(strict_types=1);

namespace Blrf\Bookstore\Model;

use Blrf\Orm\Model\Attribute as Attr;
use Blrf\Orm\Model;

/**
 * Address status model
 *
 * @todo We could probably move this class to Address\Status and set source via Attr\Source as an example.
 */
#[Attr\Model]
#[Attr\Index(type: 'PRIMARY', fields: ['status_id'])]
class AddressStatus extends Model
{
    #[Attr\Field]
    #[Attr\GeneratedValue]
    protected int $status_id;

    #[Attr\Field(type: ['type' => 'string', 'max' => 30])]
    protected string $address_status;

    public function getStatusId(): int
    {
        return $this->status_id;
    }

    public function getAddressStatus(): string
    {
        return $this->address_status;
    }
}
