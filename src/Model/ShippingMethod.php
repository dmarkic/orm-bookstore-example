<?php

declare(strict_types=1);

namespace Blrf\Bookstore\Model;

use Blrf\Orm\Model\Attribute as Attr;
use Blrf\Orm\Model;
use Blrf\Orm\Model\Meta\Data;

/**
 * ShippingMethod model
 *
 * This model is using ormHydrateModel() to switch to DerivedModel ShippintModel\Standard when
 * hydrating.
 *
 * @see ShippingModel\Standard
 */
#[Attr\Model]
#[Attr\Index(type: 'PRIMARY', fields: ['method_id'])]
class ShippingMethod extends Model
{
    #[Attr\Field]
    #[Attr\GeneratedValue]
    protected int $method_id;

    #[Attr\Field]
    protected string $method_name;

    #[Attr\Field(type: 'DECIMAL', precision: 6, scale: 2)]
    protected float $cost;

    public static function ormHydrateModel(Model $model, Data $metadata, array $data): Model
    {
        if ($data['method_name'] === 'Standard') {
            return new ShippingMethod\Standard();
        }
        return $model;
    }

    public function getMethodId(): int
    {
        return $this->method_id;
    }

    public function getMethodName(): string
    {
        return $this->method_name;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function ship(): string
    {
        return 'Shipping default';
    }
}
