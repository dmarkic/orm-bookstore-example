<?php

namespace Blrf\Bookstore\Controller;

use Blrf\Bookstore\ModelController;
use Blrf\Bookstore\Model\Country as CountryModel;

class Country extends ModelController
{
    protected function getModel(): string
    {
        return CountryModel::class;
    }
}
