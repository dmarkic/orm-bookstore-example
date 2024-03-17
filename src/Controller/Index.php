<?php

namespace Blrf\Bookstore\Controller;

use React\Http\Message\Response;

class Index
{
    public function __invoke()
    {
        return Response::plaintext(
            "You are here!\n"
        );
    }
}
