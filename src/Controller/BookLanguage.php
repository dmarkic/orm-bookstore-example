<?php

namespace Blrf\Bookstore\Controller;

use Blrf\Bookstore\ModelController;
use Blrf\Bookstore\Model\BookLanguage as BookLanguageModel;

class BookLanguage extends ModelController
{
    protected function getModel(): string
    {
        return BookLanguageModel::class;
    }
}
