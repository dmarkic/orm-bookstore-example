<?php

namespace Blrf\Bookstore\Controller;

use Blrf\Bookstore\ModelController;
use Blrf\Bookstore\Model\Address as AddressModel;

class Address extends ModelController
{
    protected function getModel(): string
    {
        return AddressModel::class;
    }
}
