<?php

namespace Blrf\Bookstore\Controller;

use Blrf\Bookstore\ModelController;
use Blrf\Bookstore\Model\Book as BookModel;

class Book extends ModelController
{
    protected function getModel(): string
    {
        return BookModel::class;
    }
}
