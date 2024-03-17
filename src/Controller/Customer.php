<?php

namespace Blrf\Bookstore\Controller;

use Blrf\Bookstore\ModelController;
use Blrf\Bookstore\Model\Customer as CustomerModel;

class Customer extends ModelController
{
    protected function getModel(): string
    {
        return CustomerModel::class;
    }
}
