<?php

namespace Blrf\Bookstore\Controller;

use Blrf\Bookstore\ModelController;
use Blrf\Bookstore\Model\ShippingMethod as ShippingMethodModel;

class ShippingMethod extends ModelController
{
    protected function getModel(): string
    {
        return ShippingMethodModel::class;
    }
}
