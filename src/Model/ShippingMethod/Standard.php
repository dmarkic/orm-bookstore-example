<?php

declare(strict_types=1);

namespace Blrf\Bookstore\Model\ShippingMethod;

use Blrf\Bookstore\Model\ShippingMethod;
use Blrf\Orm\Model\Attribute as Attr;

/**
 * ShippingMethod Standard model
 *
 * This model extends from ShippingMethod. It inherits all attributes of parent model.
 *
 * Attr\DerivedModel(parentModel) has to be defined, so attribute driver will know which
 * model to use when reading meta-data.
 */
#[Attr\DerivedModel(ShippingMethod::class)]
class Standard extends ShippingMethod
{
    public function ship(): string
    {
        return 'Shipping standard';
    }
}
