<?php

namespace Blrf\Bookstore\Controller;

use Blrf\Bookstore\ModelController;
use Blrf\Bookstore\Model\Publisher as PublisherModel;

class Publisher extends ModelController
{
    protected function getModel(): string
    {
        return PublisherModel::class;
    }
}
