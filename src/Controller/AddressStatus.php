<?php

namespace Blrf\Bookstore\Controller;

use Blrf\Bookstore\ModelController;
use Blrf\Bookstore\Model\AddressStatus as AddressStatusModel;

class AddressStatus extends ModelController
{
    protected function getModel(): string
    {
        return AddressStatusModel::class;
    }
}
